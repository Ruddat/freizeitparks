<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Park;
use App\Models\ParkQueueTime;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
            $now = Carbon::now($park->timezone ?? config('app.timezone'));
            $today = $now->toDateString();

            $opening = DB::table('park_opening_hours')
                ->where('park_id', $park->id)
                ->where('date', $today)
                ->first();

            // Park ist heute nicht geöffnet
            if (!$opening || is_null($opening->open) || is_null($opening->close)) {
                $this->info("⏳ {$park->name}: heute geschlossen ({$today})");
                continue;
            }

            $url = "https://queue-times.com/parks/{$park->queue_times_id}/queue_times.json";
            $this->info("Abruf für: {$park->name} ({$url})");

            try {
                $response = Http::timeout(10)->get($url);
                if (!$response->successful()) {
                    $this->error("Fehler bei {$park->name}");
                    continue;
                }

                $data = $response->json();

                foreach ($data['lands'] ?? [] as $land) {
                    foreach ($land['rides'] ?? [] as $ride) {
                        ParkQueueTime::updateOrCreate(
                            [
                                'park_id' => $park->id,
                                'ride_id' => $ride['id'],
                            ],
                            [
                                'ride_name'    => $ride['name'],
                                'is_open'      => $ride['is_open'],
                                'wait_time'    => $ride['wait_time'],
                                'last_updated' => Carbon::parse($ride['last_updated'])
                                    ->timezone($park->timezone ?? config('app.timezone')),
                                'land_name'    => $land['name'],
                                'fetched_at'   => now(),
                            ]
                        );
                    }
                }

                $this->info("→ gespeichert: " . count($data['lands'] ?? []) . " Bereiche");

            } catch (\Exception $e) {
                $this->error("Fehler bei {$park->name}: " . $e->getMessage());
            }
        }
    }
}
