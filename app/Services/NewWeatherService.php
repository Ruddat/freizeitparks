<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Models\ParkWeather;
use App\Models\NewWeatherForecast;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NewWeatherService
{
    protected string $baseUrl = 'https://api.weatherapi.com/v1';
    protected string $apiKey = '4e1d0d2389084771b38110150253004'; // Ersetze mit deinem WeatherAPI-Schlüssel

    public function getSevenDayForecast(float $lat = 52.5200, float $lon = 13.4050, int $parkId = 133): array
    {
        try {
            $url = $this->baseUrl . '/forecast.json?' . http_build_query([
                'key' => $this->apiKey,
                'q' => "$lat,$lon",
                'days' => 7,
                'lang' => 'de',
            ]);

            $response = Http::timeout(10)->get($url);

            if (!$response->successful() || !isset($response['forecast']['forecastday'])) {
                Log::warning('NewWeatherService: Fehlerhafte Antwort', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            $data = $response->json();
            Log::info('NewWeatherService forecast raw data', $data);
            $forecastDays = $data['forecast']['forecastday'];

            $forecast = collect($forecastDays)->map(function ($day) use ($parkId) {
                $date = Carbon::parse($day['date'])->format('Y-m-d');
                $tempDay = $day['day']['maxtemp_c'];
                $tempNight = $day['day']['mintemp_c'];
                $weatherCode = $day['day']['condition']['code'];
                $description = $day['day']['condition']['text'];
                $icon = $this->mapWeatherCodeToIcon($weatherCode);

                // NEU: Weitere Wetterdaten
                $windSpeed = $day['day']['maxwind_kph'];
                $uvIndex = $day['day']['uv'];
                $rainChance = $day['day']['daily_chance_of_rain'];

                ParkWeather::updateOrCreate(
                    ['park_id' => $parkId, 'date' => $date],
                    [
                        'temp_day' => $tempDay,
                        'temp_night' => $tempNight,
                        'weather_code' => $weatherCode,
                        'description' => $description,
                        'wind_speed' => $windSpeed,
                        'uv_index' => $uvIndex,
                        'rain_chance' => $rainChance,
                        'icon' => $icon,
                        'fetched_at' => now(),
                    ]
                );

                return [
                    'date' => Carbon::parse($day['date'])->format('D, d.m.'),
                    'temp_day' => round($tempDay),
                    'temp_night' => round($tempNight),
                    'weather_code' => $weatherCode,
                    'description' => $description,
                    'icon' => $icon,
                    'wind_speed' => round($windSpeed),
                    'uv_index' => $uvIndex,
                    'rain_chance' => $rainChance,
                ];
            })->toArray();

            return $forecast;

        } catch (Exception $e) {
            Log::error('NewWeatherService Fehler: ' . $e->getMessage());
            return [];
        }
    }


    protected function mapWeatherCodeToIcon(int $code): string
    {
        $iconMap = [
            1000 => 'clear-day.json',         // Sonnig
            1003 => 'partly-cloudy-day.json', // Leicht bewölkt
            1006 => 'cloudy.json',            // Bedeckt
            1009 => 'overcast.json',          // Stark bewölkt
            1063 => 'rain.json',              // Regen möglich
            1183 => 'rain.json',              // Regen
            1273 => 'thunder.json',           // Gewitter
            1087 => 'thunder.json',           // Gewitter möglich
            1189 => 'rain.json',              // Mäßiger Regen
            1195 => 'rain.json',              // Starker Regen
            1240 => 'rain.json',              // Leichter Regenschauer
            1243 => 'rain.json',              // Mäßiger/starker Regenschauer
            1168 => 'sleet.json',             // Gefrierender Regen
            1171 => 'sleet.json',             // Starker gefrierender Regen
            1066 => 'snow.json',              // Schneefall möglich
            1213 => 'snow.json',              // Leichter Schneefall
            1219 => 'snow.json',              // Mäßiger Schneefall
            1225 => 'snow.json',              // Starker Schneefall
            1030 => 'fog.json',               // Nebel
        ];

        return $iconMap[$code] ?? 'not-available.json';
    }
}
