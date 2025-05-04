<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Park;
use App\Models\ParkQueueTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\ParkQueueTimeAverages;

class ImportParkQueueTimes extends Command
{
    protected $signature = 'parks:import-queue-times';
    protected $description = 'Importiert aktuelle Wartezeiten von Queue-Times.com und speichert Durchschnittswerte';

    public function handle(): void
    {
        $parks = Park::whereNotNull('queue_times_id')
            ->where('status', 'active')
            ->get();

        foreach ($parks as $index => $park) {
            $this->processPark($park);

            if ($index < $parks->count() - 1) {
                sleep(1);
            }
        }
    }

    protected function processPark(Park $park): void
    {
        $timezone = $this->validateTimezone($park->timezone ?? config('app.timezone'));
        $now = Carbon::now($timezone);
        $today = $now->toDateString();

        if (!$this->isParkOpen($park, $today)) {
            $this->info("⏳ {$park->name}: heute geschlossen ({$today})");
            return;
        }

        $url = "https://queue-times.com/parks/{$park->queue_times_id}/queue_times.json";
        $this->info("Abruf für: {$park->name} ({$url})");

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

    protected function isParkOpen(Park $park, string $today): bool
    {
        $opening = DB::table('park_opening_hours')
            ->where('park_id', $park->id)
            ->where('date', $today)
            ->first();

        return $opening && !is_null($opening->open) && !is_null($opening->close);
    }

    protected function fetchQueueTimes(string $url, string $parkName): ?array
    {
        try {
            $response = Http::retry(3, 1000)
                ->timeout(10)
                ->get($url);

            if (!$response->successful()) {
                $this->error("Fehler bei {$parkName}: HTTP {$response->status()}");
                Log::error("API-Fehler bei {$parkName}: HTTP {$response->status()}");
                return null;
            }

            $data = $response->json();
            Log::info("API-Daten für {$parkName}: " . json_encode($data));
            $this->info("API-Daten für {$parkName} geloggt.");

            if (!is_array($data) || (empty($data['lands']) && empty($data['rides']))) {
                $this->warn("Ungültige oder leere Daten für {$parkName}: Keine Fahrgeschäfte vorhanden.");
                return null;
            }

            return $data;
        } catch (\Exception $e) {
            Log::error("Fehler bei {$parkName}: {$e->getMessage()}");
            $this->error("Fehler bei {$parkName}: {$e->getMessage()}");
            return null;
        }
    }

    protected function saveQueueTimes(Park $park, array $data, string $timezone): void
    {
        DB::transaction(function () use ($park, $data, $timezone) {
            $count = 0;

            // Verarbeite Rides innerhalb von Lands
            if (!empty($data['lands'])) {
                foreach ($data['lands'] as $land) {
                    foreach ($land['rides'] ?? [] as $ride) {
                        $this->saveRide($park, $ride, $land['name'] ?? 'Unbekannt', $timezone);
                        $count++;
                    }
                }
            }

            // Verarbeite Rides auf oberster Ebene
            if (!empty($data['rides'])) {
                foreach ($data['rides'] as $ride) {
                    $this->saveRide($park, $ride, 'Unbekannt', $timezone); // Kein Landname verfügbar
                    $count++;
                }
            }

            $this->info("→ gespeichert: {$count} Fahrgeschäfte");
        });
    }

    protected function saveRide(Park $park, array $ride, string $landName, string $timezone): void
    {
        ParkQueueTime::updateOrCreate(
            [
                'park_id' => $park->id,
                'ride_id' => $ride['id'],
            ],
            [
                'ride_name'    => $ride['name'] ?? 'Unbekannt',
                'is_open'      => $ride['is_open'] ?? false,
                'wait_time'    => $ride['wait_time'] ?? 0,
                'last_updated' => $this->parseLastUpdated($ride['last_updated'] ?? now(), $timezone),
                'land_name'    => $landName,
                'fetched_at'   => now(),
            ]
        );

        $this->updateAverageWaitTime($park->id, $ride, $landName);
    }

    protected function updateAverageWaitTime(int $parkId, array $ride, string $landName): void
    {
        $waitTime = $ride['wait_time'] ?? 0;

        $average = ParkQueueTimeAverages::firstOrNew([
            'park_id' => $parkId,
            'ride_id' => $ride['id'],
        ]);

        if (!$average->exists) {
            $average->ride_name = $ride['name'] ?? 'Unbekannt';
            $average->land_name = $landName;
            $average->average_wait_time = $waitTime;
            $average->fetch_count = 1;
        } else {
            $currentAvg = $average->average_wait_time;
            $currentCount = $average->fetch_count;
            $newCount = $currentCount + 1;
            $newAvg = (($currentAvg * $currentCount) + $waitTime) / $newCount;

            $average->average_wait_time = $newAvg;
            $average->fetch_count = $newCount;
        }

        $average->save();
    }

    protected function parseLastUpdated($lastUpdated, string $timezone): Carbon
    {
        try {
            return Carbon::parse($lastUpdated)->timezone($timezone);
        } catch (\Exception $e) {
            Log::warning("Ungültiges Datum '{$lastUpdated}', verwende aktuelle Zeit.");
            return Carbon::now($timezone);
        }
    }
}
