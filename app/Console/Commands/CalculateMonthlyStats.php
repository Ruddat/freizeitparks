<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\ParkDailyStats;
use App\Models\ParkMonthlyStat;
use Illuminate\Console\Command;

class CalculateMonthlyStats extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:calculate-monthly-stats';

    /**
     * The console command description.
     */
    protected $description = 'Berechnet monatliche Durchschnittswerte für alle Parks aus den täglichen Stats';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("⏳ Monatsauswertung startet...");

        $now = Carbon::now();
        $firstDay = $now->copy()->startOfMonth()->toDateString();
        $lastDay = $now->copy()->endOfMonth()->toDateString();

        $parks = ParkDailyStats::distinct()->pluck('park_id');

        foreach ($parks as $parkId) {
            $avgCrowd = ParkDailyStats::where('park_id', $parkId)
                ->whereBetween('date', [$firstDay, $lastDay])
                ->avg('avg_crowd_level');

            ParkMonthlyStat::updateOrCreate(
                [
                    'park_id' => $parkId,
                    'year' => $now->year,
                    'month' => $now->month,
                ],
                [
                    'avg_crowd_level' => round($avgCrowd),
                ]
            );

            $this->info("✅ Park-ID $parkId: Monatsdurchschnitt gespeichert (Crowd-Level: " . round($avgCrowd) . ")");
        }

        $this->info("🎉 Monatsauswertung abgeschlossen.");
    }
}
