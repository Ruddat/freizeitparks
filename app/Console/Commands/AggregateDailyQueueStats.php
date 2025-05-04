<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\ParkQueueTimeLog;
use App\Models\ParkDailyRideStats;
use Illuminate\Support\Facades\DB;

class AggregateDailyQueueStats extends Command
{
    protected $signature = 'parks:aggregate-daily-stats';
    protected $description = 'Berechnet tägliche Durchschnittswerte der Wartezeiten für alle Parks und Attraktionen.';

    public function handle(): void
    {
        $yesterday = Carbon::yesterday()->startOfDay();
        $today = $yesterday->copy()->endOfDay();

        $this->info("📊 Aggregation für {$yesterday->toDateString()} gestartet...");

        $stats = ParkQueueTimeLog::query()
            ->whereBetween('fetched_at', [$yesterday, $today])
            ->selectRaw('park_id, ride_id, ride_name, DATE(fetched_at) as date, ROUND(AVG(wait_time),1) as avg_wait')
            ->groupBy('park_id', 'ride_id', 'ride_name', 'date')
            ->get();

        foreach ($stats as $stat) {
            ParkDailyRideStats::updateOrCreate(
                [
                    'park_id'  => $stat->park_id,
                    'ride_id'  => $stat->ride_id,
                    'date'     => $stat->date,
                ],
                [
                    'ride_name'      => $stat->ride_name,
                    'avg_wait_time'  => $stat->avg_wait,
                ]
            );
        }

        $this->info("✅ {$stats->count()} Tagesstatistiken gespeichert.");
    }
}
