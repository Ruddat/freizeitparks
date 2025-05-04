<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Park;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\ParkQueueTimeLog;

class ImportParkQueueTimes extends Command
{
    protected $signature = 'parks:import-queue-times';
    protected $description = 'Importiert aktuelle Wartezeiten und speichert den zeitlichen Verlauf, wenn der Park geöffnet ist.';

    public function handle(): void
    {
        $parks = Park::whereNotNull('queue_times_id')
            ->where('status', 'active')
            ->get();

        foreach ($parks as $index => $park) {
            $this->processPark($park);

            if ($index < $parks->count() - 1) {
                sleep(1); // API nicht überlasten
            }
        }
    }

    protected function processPark(Park $park): void
    {
        $timezone = $this->validateTimezone($park->timezone ?? config('app.timezone'));
        $now = Carbon::now($timezone);
        $today = $now->toDateString();

        if (!$this->isParkOpen($park, $today)) {
            $this->info("⏳ {$park->name}: aktuell geschlossen ({$today}) [Zeitzone: {$timezone}]");
            return;
        }

        $url = "https://queue-times.com/parks/{$park->queue_times_id}/queue_times.json";
        $this->info("🔄 Abruf für: {$park->name} ({$url})");

        $data = $this->fetchQueueTimes($url, $park->name);
        if (!$data) {
            return;
        }

        $this->saveQueueTimes($park, $data, $timezone);
    }

    protected function validateTimezone(string $timezone): string
    {
        return in_array($timezone, timezone_identifiers_list()) ? $timezone : config('app.timezone');
    }

    protected function isParkOpen(Park $park, ?string $date = null): bool
    {
        $timezone = $this->validateTimezone($park->timezone ?? config('app.timezone'));
        $now = Carbon::now($timezone);
        $date = $date ?? $now->toDateString();

        $opening = DB::table('park_opening_hours')
            ->where('park_id', $park->id)
            ->where('date', $date)
            ->first();

        if (!$opening || !$opening->open || !$opening->close) {
            return false;
        }

        try {
            $openTime = Carbon::createFromFormat('H:i:s', $opening->open, $timezone)->setDateFrom($now);
            $closeTime = Carbon::createFromFormat('H:i:s', $opening->close, $timezone)->setDateFrom($now);

            if ($closeTime->lessThan($openTime)) {
                $closeTime->addDay(); // über Mitternacht geöffnet
            }

            return $now->between($openTime, $closeTime);
        } catch (\Exception $e) {
            Log::warning("⚠️ Ungültige Öffnungszeiten für Park #{$park->id}: {$e->getMessage()}");
            return false;
        }
    }

    protected function fetchQueueTimes(string $url, string $parkName): ?array
    {
        try {
            $response = Http::retry(3, 1000)
                ->timeout(10)
                ->get($url);

            if (!$response->successful()) {
                $this->error("❌ Fehler bei {$parkName}: HTTP {$response->status()}");
                Log::error("API-Fehler bei {$parkName}: HTTP {$response->status()}");
                return null;
            }

            $data = $response->json();
            Log::info("📦 API-Daten für {$parkName}: " . json_encode($data));
            $this->info("✅ Daten erfolgreich abgerufen.");

            if (!is_array($data) || (empty($data['lands']) && empty($data['rides']))) {
                $this->warn("⚠️ Keine gültigen Fahrgeschäfte für {$parkName} gefunden.");
                return null;
            }

            return $data;
        } catch (\Exception $e) {
            Log::error("❌ Fehler bei {$parkName}: {$e->getMessage()}");
            $this->error("❌ Fehler bei {$parkName}: {$e->getMessage()}");
            return null;
        }
    }

    protected function saveQueueTimes(Park $park, array $data, string $timezone): void
    {
        DB::transaction(function () use ($park, $data, $timezone) {
            $count = 0;

            foreach ($data['lands'] ?? [] as $land) {
                foreach ($land['rides'] ?? [] as $ride) {
                    $this->saveRideLog($park, $ride, $land['name'] ?? 'Unbekannt', $timezone);
                    $count++;
                }
            }

            foreach ($data['rides'] ?? [] as $ride) {
                $this->saveRideLog($park, $ride, 'Unbekannt', $timezone);
                $count++;
            }

            $this->info("💾 {$count} Fahrgeschäfte gespeichert für {$park->name}.");
        });
    }

    protected function saveRideLog(Park $park, array $ride, string $landName, string $timezone): void
    {
        try {
            ParkQueueTimeLog::create([
                'park_id'    => $park->id,
                'ride_id'    => $ride['id'],
                'ride_name'  => $ride['name'] ?? 'Unbekannt',
                'land_name'  => $landName,
                'wait_time'  => $ride['wait_time'] ?? 0,
                'is_open'    => $ride['is_open'] ?? false,
                'fetched_at' => $this->parseLastUpdated($ride['last_updated'] ?? now(), $timezone),
                'created_at' => now($timezone),
                'updated_at' => now($timezone),
            ]);
        } catch (\Exception $e) {
            Log::error("❌ Fehler beim Speichern von Ride [{$ride['id']}] im Park {$park->name}: {$e->getMessage()}");
        }
    }

    protected function parseLastUpdated($lastUpdated, string $timezone): Carbon
    {
        try {
            return Carbon::parse($lastUpdated)->timezone($timezone);
        } catch (\Exception $e) {
            Log::warning("⚠️ Ungültiges Datum '{$lastUpdated}', verwende aktuelle Zeit.");
            return Carbon::now($timezone);
        }
    }
}
