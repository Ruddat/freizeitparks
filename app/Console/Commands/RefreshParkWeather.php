<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Park;
use App\Models\ParkWeather;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Cache;

class RefreshParkWeather extends Command
{
    protected $signature = 'parks:refresh-weather';
    protected $description = 'Aktualisiert die Wetterdaten für alle aktiven Freizeitparks.';

    protected WeatherService $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        parent::__construct();
        $this->weatherService = $weatherService;
    }

    public function handle()
    {
        $parks = Park::where('status', 'active')->get();
        $this->info('🌦️ Aktualisiere Wetterdaten für ' . $parks->count() . ' Parks...');

        $count = 0;

        foreach ($parks as $park) {
            try {
                $forecast = $this->weatherService->getForecastForCoordinates($park->latitude, $park->longitude);

                foreach ($forecast as $day) {
                    ParkWeather::updateOrCreate(
                        ['park_id' => $park->id, 'date' => $day['date']],
                        [
                            'temp_day'    => round($day['temp_day'], 1),
                            'temp_night'  => round($day['temp_night'], 1),
                            'weather_code'=> $day['weather_code'],
                            'description' => $this->getWeatherDescription($day['weather_code']),
                            'icon'        => $this->getWeatherIcon($day['weather_code']),
                            'fetched_at'  => now(),
                        ]
                    );
                }

                // Cache für den Park löschen, damit er frisch gezogen wird
                Cache::forget('park_forecast_' . $park->id);

                $count++;
                $this->info('✅ Wetter für Park: ' . $park->name . ' aktualisiert.');

            } catch (\Exception $e) {
                $this->error('❌ Fehler bei Park: ' . $park->name . ' - ' . $e->getMessage());
            }
        }

        $this->info('🎉 Fertig! Wetterdaten für ' . $count . ' Parks aktualisiert.');
        return 0;
    }

    protected function getWeatherDescription(?int $code): ?string
    {
        return [
            0 => 'Sonnig klar',
            1 => 'Teilweise bewölkt',
            2 => 'Wolkig',
            3 => 'Bedeckt',
            45 => 'Nebel',
            48 => 'Nebel mit Reif',
            51 => 'Leichter Sprühregen',
            53 => 'Mäßiger Sprühregen',
            55 => 'Starker Sprühregen',
            56 => 'Leichter gefrierender Sprühregen',
            57 => 'Starker gefrierender Sprühregen',
            61 => 'Leichter Regen',
            63 => 'Mäßiger Regen',
            65 => 'Starker Regen',
            66 => 'Leichter gefrierender Regen',
            67 => 'Starker gefrierender Regen',
            71 => 'Leichter Schneefall',
            73 => 'Mäßiger Schneefall',
            75 => 'Starker Schneefall',
            77 => 'Schneekristalle',
            80 => 'Leichter Regenschauer',
            81 => 'Regenschauer',
            82 => 'Starke Regenschauer',
            85 => 'Leichte Schneeschauer',
            86 => 'Starke Schneeschauer',
            95 => 'Gewitter',
            96 => 'Gewitter mit leichtem Regen',
            99 => 'Gewitter mit starkem Regen',
        ][$code] ?? null;
    }

    protected function getWeatherIcon(?int $code): ?string
    {
        $icons = [
            0 => ['day' => 'clear-day.svg', 'night' => 'clear-night.svg'],
            1 => ['day' => 'partly-cloudy-day.svg', 'night' => 'partly-cloudy-night.svg'],
            2 => ['day' => 'partly-cloudy-day.svg', 'night' => 'partly-cloudy-night.svg'],
            3 => ['day' => 'overcast-day.svg', 'night' => 'overcast-night.svg'],
            45 => ['day' => 'fog-day.svg', 'night' => 'fog-night.svg'],
            48 => ['day' => 'fog-day.svg', 'night' => 'fog-night.svg'],
            51 => ['day' => 'drizzle.svg', 'night' => 'drizzle.svg'],
            53 => ['day' => 'drizzle.svg', 'night' => 'drizzle.svg'],
            55 => ['day' => 'drizzle.svg', 'night' => 'drizzle.svg'],
            56 => ['day' => 'sleet.svg', 'night' => 'sleet.svg'],
            57 => ['day' => 'sleet.svg', 'night' => 'sleet.svg'],
            61 => ['day' => 'rain.svg', 'night' => 'rain.svg'],
            63 => ['day' => 'rain.svg', 'night' => 'rain.svg'],
            65 => ['day' => 'rain.svg', 'night' => 'rain.svg'],
            66 => ['day' => 'sleet.svg', 'night' => 'sleet.svg'],
            67 => ['day' => 'sleet.svg', 'night' => 'sleet.svg'],
            71 => ['day' => 'snow.svg', 'night' => 'snow.svg'],
            73 => ['day' => 'snow.svg', 'night' => 'snow.svg'],
            75 => ['day' => 'snow.svg', 'night' => 'snow.svg'],
            77 => ['day' => 'snow.svg', 'night' => 'snow.svg'],
            80 => ['day' => 'rain.svg', 'night' => 'rain.svg'],
            81 => ['day' => 'rain.svg', 'night' => 'rain.svg'],
            82 => ['day' => 'rain.svg', 'night' => 'rain.svg'],
            85 => ['day' => 'snow.svg', 'night' => 'snow.svg'],
            86 => ['day' => 'snow.svg', 'night' => 'snow.svg'],
            95 => ['day' => 'thunderstorms-day.svg', 'night' => 'thunderstorms-night.svg'],
            96 => ['day' => 'thunderstorms-day-rain.svg', 'night' => 'thunderstorms-night-rain.svg'],
            99 => ['day' => 'thunderstorms-day-rain.svg', 'night' => 'thunderstorms-night-rain.svg'],
        ];

        $isDay = now()->hour >= 6 && now()->hour < 20;
        return $icons[$code][$isDay ? 'day' : 'night'] ?? 'not-available.svg';
    }
}
