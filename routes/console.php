<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\CheckMaintenanceExpiration;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Wartungsmodus-Check
Schedule::command(CheckMaintenanceExpiration::class)->everyFiveMinutes();
Schedule::command('app:calculate-monthly-stats')->dailyAt('00:10')->withoutOverlapping();
