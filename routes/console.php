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
Schedule::command('parks:refresh-weather')->dailyAt('01:10')->withoutOverlapping();


// Backup-Check
Schedule::command('backup:run')->dailyAt('02:00')->withoutOverlapping();
Schedule::command('backup:monitor')->dailyAt('02:05')->withoutOverlapping();
Schedule::command('backup:clean')->dailyAt('02:10')->withoutOverlapping();


Schedule::command('holidays:import ' . (now()->addYear()->year))
->yearlyOn(1, 5, '02:00') // 5. Januar um 2 Uhr morgens
->timezone('Europe/Berlin');

Schedule::command('parks:generate-crowd-reports')->dailyAt('02:30')->withoutOverlapping();
Schedule::command('blog:generate-daily-post')->dailyAt('03:00')->withoutOverlapping();

Schedule::command('parks:import-queue-times')
    ->hourly()
    ->withoutOverlapping();
    
