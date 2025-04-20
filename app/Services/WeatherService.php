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
    public function getSevenDayForecast(float $lat = 52.5200, float $lon = 13.4050): array
    {
        try {
            $url = $this->baseUrl . '?' . http_build_query([
                'latitude' => $lat,
                'longitude' => $lon,
                'daily' => 'temperature_2m_max,temperature_2m_min,weathercode',
                'timezone' => 'Europe/Berlin',
            ]);

            $response = Http::timeout(10)->get($url);

//dd($response->body());

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


    public function getForecastForCoordinates(float $lat, float $lon): array
    {
        $response = Http::timeout(10)->get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $lat,
            'longitude' => $lon,
            'daily' => 'temperature_2m_max,temperature_2m_min,weathercode',
            'timezone' => 'auto',
        ]);

        if (!$response->successful()) {
            \Log::warning('Wetter konnte nicht geladen werden', ['lat' => $lat, 'lon' => $lon]);
            return [];
        }

        $data = $response->json();

        return collect($data['daily']['time'] ?? [])->map(function ($date, $i) use ($data) {
            return [
                'date' => $date,
                'temp_day' => $data['daily']['temperature_2m_max'][$i],
                'temp_night' => $data['daily']['temperature_2m_min'][$i],
                'weather_code' => $data['daily']['weathercode'][$i],
            ];
        })->take(4)->toArray(); // nur 4 Tage für Vorschau
    }


}
