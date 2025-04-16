<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Park;
use Illuminate\Support\Facades\Log;

class FetchParkStatus extends Command
{
    protected $signature = 'parks:fetch-status';
    protected $description = 'Fetch current open/closed status for parks from Queue-Times API';

    public function handle()
    {
        $this->info('Fetching park statuses...');

        // Alle Parks mit queue_times_id abrufen
        $parks = Park::whereNotNull('queue_times_id')->get();

        if ($parks->isEmpty()) {
            $this->error('No parks with queue_times_id found in database.');
            return;
        }

        foreach ($parks as $park) {
            try {
                $status = $this->fetchParkStatus($park->queue_times_id);
                $park->update([
                    'status' => $status,
                    'updated_at' => now(),
                ]);
                $this->info("Updated {$park->name}: Status = {$status}");
            } catch (\Exception $e) {
                Log::error("Failed to fetch status for park {$park->name} (ID: {$park->queue_times_id}): {$e->getMessage()}");
                $this->error("Failed to fetch status for {$park->name}: {$e->getMessage()}");
                // Fallback: Status auf 'unknown' setzen
                $park->update([
                    'status' => 'unknown',
                    'updated_at' => now(),
                ]);
            }
        }

        $this->info('Park statuses updated successfully!');
    }

    protected function fetchParkStatus($queueTimesId)
    {
        $url = "https://queue-times.com/parks/{$queueTimesId}/queue_times.json";
        $response = Http::timeout(10)->get($url);

        if (!$response->successful()) {
            throw new \Exception("API request failed with status {$response->status()}");
        }

        $data = $response->json();

        // Prüfen, ob 'lands' und 'rides' vorhanden sind
        if (!isset($data['lands']) || !is_array($data['lands'])) {
            throw new \Exception("Invalid API response: No lands data");
        }

        $openRides = 0;
        $totalRides = 0;

        // Alle Fahrgeschäfte durchlaufen
        foreach ($data['lands'] as $land) {
            if (!isset($land['rides']) || !is_array($land['rides'])) {
                continue;
            }

            foreach ($land['rides'] as $ride) {
                $totalRides++;
                if (isset($ride['is_open']) && $ride['is_open'] === true) {
                    $openRides++;
                }
            }
        }

        // Status ableiten
        if ($totalRides === 0) {
            return 'unknown'; // Keine Fahrgeschäfte, Status unklar
        }

        // Wenn mindestens ein Fahrgeschäft offen ist, gilt der Park als offen
        if ($openRides > 0) {
            return 'open';
        }

        return 'closed';
    }
}
