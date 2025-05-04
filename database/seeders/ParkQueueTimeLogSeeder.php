<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ParkQueueTimeLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rides = [
            // Phantasialand (park_id = 61)
            ["park_id" => 61, "ride_id" => 6804, "ride_name" => "Bolles Flugschule", "land_name" => "Berlin"],
            ["park_id" => 61, "ride_id" => 6809, "ride_name" => "Bolles Riesenrad", "land_name" => "Berlin"],
            ["park_id" => 61, "ride_id" => 6829, "ride_name" => "Das verrückte Hotel Tartüff", "land_name" => "Berlin"],
            ["park_id" => 61, "ride_id" => 6819, "ride_name" => "Maus au Chocolat", "land_name" => "Berlin"],
            ["park_id" => 61, "ride_id" => 6825, "ride_name" => "Black Mamba", "land_name" => "Deep in Africa"],

            // Moviepark (park_id = 68)
            ["park_id" => 68, "ride_id" => 7701, "ride_name" => "Star Trek: Operation Enterprise", "land_name" => "Federation Plaza"],
            ["park_id" => 68, "ride_id" => 7702, "ride_name" => "The Lost Temple", "land_name" => "Adventure Lagoon"],
            ["park_id" => 68, "ride_id" => 7703, "ride_name" => "Van Helsing’s Factory", "land_name" => "Streets of New York"],
        ];

        $startDate = Carbon::create(2025, 4, 1);
        $endDate = Carbon::create(2025, 4, 30);
        $hours = range(10, 18); // jede Stunde zwischen 10:00 und 18:00 Uhr

        $entries = [];

        foreach ($rides as $ride) {
            $date = $startDate->copy();
            while ($date <= $endDate) {
                foreach ($hours as $hour) {
                    $timestamp = $date->copy()->setHour($hour)->setMinute(0)->setSecond(0);
                    $entries[] = [
                        'park_id'    => $ride['park_id'],
                        'ride_id'    => $ride['ride_id'],
                        'ride_name'  => $ride['ride_name'],
                        'land_name'  => $ride['land_name'],
                        'wait_time'  => rand(0, 90),
                        'is_open'    => rand(1, 100) > 10, // 90% offen
                        'fetched_at' => $timestamp,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }
                $date->addDay();
            }
        }

        DB::table('park_queue_time_logs')->insert($entries);
    }
}
