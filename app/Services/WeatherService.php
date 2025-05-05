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
     * Liefert erweiterte 7-Tage-Vorhersage für feste oder angegebene Koordinaten.
     */
    public function getSevenDayForecast(float $lat = 52.5200, float $lon = 13.4050): array
    {
        try {
            $url = $this->baseUrl . '?' . http_build_query([
                'latitude' => $lat,
                'longitude' => $lon,
                'daily' => implode(',', [
                    'temperature_2m_max',
                    'temperature_2m_min',
                    'temperature_2m_mean',
                    'apparent_temperature_max',
                    'apparent_temperature_min',
                    'apparent_temperature_mean',
                    'precipitation_sum',
                    'rain_sum',
                    'showers_sum',
                    'snowfall_sum',
                    'precipitation_hours',
                    'precipitation_probability_max',
                    'precipitation_probability_mean',
                    'precipitation_probability_min',
                    'weathercode',
                    'sunrise',
                    'sunset',
                    'sunshine_duration',
                    'daylight_duration',
                    'wind_speed_10m_max',
                    'wind_gusts_10m_max',
                    'wind_direction_10m_dominant',
                    'shortwave_radiation_sum',
                    'et0_fao_evapotranspiration',
                    'uv_index_max',
                    'uv_index_clear_sky_max',
                ]),
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
            $daily = $data['daily'];
            $dates = $daily['time'] ?? [];

            return collect($dates)->take(7)->map(function ($date, $i) use ($daily) {
                return [
                    'date' => Carbon::parse($date)->format('D, d.m.'),
                    'temp_day' => round($daily['temperature_2m_max'][$i]),
                    'temp_night' => round($daily['temperature_2m_min'][$i]),
                    'temp_mean' => $daily['temperature_2m_mean'][$i] ?? null,
                    'apparent_temp_max' => $daily['apparent_temperature_max'][$i] ?? null,
                    'apparent_temp_min' => $daily['apparent_temperature_min'][$i] ?? null,
                    'apparent_temp_mean' => $daily['apparent_temperature_mean'][$i] ?? null,
                    'rain_sum' => $daily['rain_sum'][$i] ?? null,
                    'showers_sum' => $daily['showers_sum'][$i] ?? null,
                    'snowfall_sum' => $daily['snowfall_sum'][$i] ?? null,
                    'precipitation_sum' => $daily['precipitation_sum'][$i] ?? null,
                    'precipitation_hours' => $daily['precipitation_hours'][$i] ?? null,
                    'precip_prob_max' => $daily['precipitation_probability_max'][$i] ?? null,
                    'precip_prob_mean' => $daily['precipitation_probability_mean'][$i] ?? null,
                    'precip_prob_min' => $daily['precipitation_probability_min'][$i] ?? null,
                    'sunrise' => $daily['sunrise'][$i] ?? null,
                    'sunset' => $daily['sunset'][$i] ?? null,
                    'sunshine_duration' => $daily['sunshine_duration'][$i] ?? null,
                    'daylight_duration' => $daily['daylight_duration'][$i] ?? null,
                    'wind_speed' => $daily['wind_speed_10m_max'][$i] ?? null,
                    'wind_gusts' => $daily['wind_gusts_10m_max'][$i] ?? null,
                    'wind_direction' => $daily['wind_direction_10m_dominant'][$i] ?? null,
                    'radiation_sum' => $daily['shortwave_radiation_sum'][$i] ?? null,
                    'evapotranspiration' => $daily['et0_fao_evapotranspiration'][$i] ?? null,
                    'uv_index' => $daily['uv_index_max'][$i] ?? null,
                    'uv_index_clear_sky' => $daily['uv_index_clear_sky_max'][$i] ?? null,
                    'weather_code' => $daily['weathercode'][$i],
                    'icon' => $this->getWeatherIconFromCode($daily['weathercode'][$i]),
                ];
            })->toArray();

        } catch (Exception $e) {
            Log::error('Open-Meteo Fehler: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Alias für Koordinatenabfrage (z. B. für Parks)
     */
    public function getForecastForCoordinates(float $lat, float $lon): array
    {
        return $this->getSevenDayForecast($lat, $lon);
    }

    /**
     * Wandelt Wettercode in Icon (eigener Pfad oder Open-Meteo-kompatibel).
     */
    protected function getWeatherIconFromCode(int $code): string
    {
        return asset('icons/weather/' . $code . '.png'); // z. B. /public/icons/weather/3.png
    }
}
