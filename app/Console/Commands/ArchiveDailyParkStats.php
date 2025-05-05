<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Park;
use App\Models\ParkDailyStats;
use App\Models\ParkWeather;
use App\Models\ParkQueueTimeLog;
use App\Models\ParkCrowdReport;
use Carbon\Carbon;

class ArchiveDailyParkStats extends Command
{
    protected $signature = 'parks:archive-daily-stats';
    protected $description = 'Aggregiert tägliche Wetter- und Auslastungsdaten je Park in park_daily_stats';

    public function handle(): void
    {
        $yesterday = now()->subDay()->toDateString();
        $parks = Park::where('status', 'active')->get();
        $count = 0;

        foreach ($parks as $park) {
            $weatherQuery = ParkWeather::where('park_id', $park->id)
                ->where('date', $yesterday);

            $waitLogs = ParkQueueTimeLog::where('park_id', $park->id)
                ->whereDate('fetched_at', $yesterday);

            $crowdReports = ParkCrowdReport::where('park_id', $park->id)
                ->whereDate('created_at', $yesterday);

            if (!$weatherQuery->exists() && !$waitLogs->exists()) {
                $this->warn("⏭️  {$park->name}: Keine Daten für {$yesterday}");
                continue;
            }


            // ⏱ Zusätzliche Wartezeit-Auswertungen
            $avgWaitTime = $waitLogs->avg('wait_time');
            $maxWaitTime = $waitLogs->max('wait_time');

            $totalRides = $waitLogs->distinct('ride_id')->count('ride_id');
            $openRides = $waitLogs->where('is_open', true)->distinct('ride_id')->count('ride_id');
            $ridesOpenRatio = $totalRides > 0 ? round(($openRides / $totalRides) * 100, 1) : null;


            ParkDailyStats::updateOrCreate(
                ['park_id' => $park->id, 'date' => $yesterday],
                [
                    'avg_temp_day'        => round($weatherQuery->avg('temp_day'), 1),
                    'avg_temp_night'      => round($weatherQuery->avg('temp_night'), 1),
                    'avg_precip_prob'     => round($weatherQuery->avg('rain_chance')),
                    'precipitation_sum'   => round($weatherQuery->avg('precipitation'), 1),
                    'sunshine_duration'   => round($weatherQuery->avg('sunshine_duration')),
                    'wind_speed_max'      => round($weatherQuery->max('wind_speed'), 1),
                    'wind_gusts_max'      => round($weatherQuery->max('wind_gusts'), 1),
                    'uv_index_max'        => round($weatherQuery->max('uv_index'), 1),

                    'avg_crowd_level'     => $crowdReports->exists() ? round($crowdReports->avg('crowd_level')) : null,
                    'crowd_sample_count'  => $crowdReports->count(),

                    'weather_code'        => $weatherQuery->value('weather_code'),
                    'description'         => $weatherQuery->value('description'),

                    'avg_wait_time'      => round($avgWaitTime, 1),
                    'max_wait_time'      => $maxWaitTime,
                    'rides_open_ratio'   => $ridesOpenRatio,
                ]
            );

            $this->info("✅ {$park->name}: Daten für {$yesterday} archiviert.");
            $count++;
        }

        $this->info("🎉 Fertig: {$count} Parks archiviert.");
    }
}
