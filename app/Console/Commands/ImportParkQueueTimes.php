<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Park;
use App\Models\ParkQueueTime;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportParkQueueTimes extends Command
{
    protected $signature = 'parks:import-queue-times';
    protected $description = 'Importiert aktuelle Wartezeiten von Queue-Times.com (nur für aktive und geöffnete Parks)';

    public function handle(): void
    {
        $parks = Park::whereNotNull('queue_times_id')
            ->where('status', 'active')
            ->get();

        foreach ($parks as $park) {
            $this->processPark($park);
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
            $response = Http::retry(3, 1000) // 3 Versuche, 1 Sekunde Pause
                ->timeout(10)
                ->get($url);

            if (!$response->successful()) {
                $this->error("Fehler bei {$parkName}: HTTP {$response->status()}");
                return null;
            }

            return $response->json();
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

            foreach ($data['lands'] ?? [] as $land) {
                foreach ($land['rides'] ?? [] as $ride) {
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
                            'land_name'    => $land['name'] ?? 'Unbekannt',
                            'fetched_at'   => now(),
                        ]
                    );
                    $count++;
                }
            }

            $this->info("→ gespeichert: {$count} Fahrgeschäfte");
        });
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
