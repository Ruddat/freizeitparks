<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;



class WeatherService
{
    protected string $baseUrl = 'https://api.open-meteo.com/v1/forecast';

    /**
     * Gibt eine 7-Tage-Wettervorhersage für die Startseite zurück.
     */
    public function getSevenDayForecast(float $lat = 50.1109, float $lon = 8.6821): array
    {
        try {
            $url = $this->baseUrl . '?' . http_build_query([
                'latitude' => $lat,
                'longitude' => $lon,
                'daily' => 'temperature_2m_max,temperature_2m_min,weathercode',
                'timezone' => 'Europe/Berlin',
            ]);

            $response = Http::timeout(10)->get($url);

            if (!$response->successful() || !isset($response['daily'])) {
                Log::warning('Open-Meteo: Fehlerhafte Antwort', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            $data = $response->json();
            $dates = $data['daily']['time'] ?? [];
            $tempsMax = $data['daily']['temperature_2m_max'] ?? [];
            $tempsMin = $data['daily']['temperature_2m_min'] ?? [];
            $codes = $data['daily']['weathercode'] ?? [];

            return collect($dates)->take(7)->map(function ($date, $i) use ($tempsMax, $tempsMin, $codes) {
                return [
                    'date' => Carbon::parse($date)->format('D, d.m.'),
                    'temp_day' => round($tempsMax[$i]),
                    'temp_night' => round($tempsMin[$i]),
                    'weather_code' => $codes[$i],
                    'icon' => $this->getWeatherIconFromCode($codes[$i]),
                ];
            })->toArray();

        } catch (\Exception $e) {
            Log::error('Open-Meteo Fehler: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Wandelt Weathercode in Icon-URL um (nach Open-Meteo-Standard oder eigene Icons).
     */
    protected function getWeatherIconFromCode(int $code): string
    {
        // Du kannst hier deine eigenen Icons verwenden
        return asset('icons/weather/' . $code . '.png'); // z. B. public/icons/weather/2.png
    }
}
