<?php

namespace App\Http\Controllers\Frontend;

use Carbon\Carbon;
use App\Models\Park;
use Illuminate\Http\Request;
use App\Services\WeatherService;
use App\Http\Controllers\Controller;
use App\Services\ParkScraperService;
use Illuminate\Support\Facades\Storage;

class IndexController extends Controller
{
    protected WeatherService $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function index()
    {
        $rawForecast = $this->weatherService->getSevenDayForecast();

        $weatherIcons = [
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

        $weatherDescriptions = [
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
        ];

        $forecast = collect($rawForecast)->map(function ($item) use ($weatherIcons, $weatherDescriptions) {
            $code = $item['weather_code'] ?? null;
            $isDay = now()->format('H') >= 6 && now()->format('H') < 20;
            $icon = $weatherIcons[$code][$isDay ? 'day' : 'night'] ?? 'not-available.svg';

            return [
                'date'        => $item['date'],
                'temp_day'    => $item['temp_day'],
                'temp_night'  => $item['temp_night'],
                'icon' => asset('images/weather/animated/' . $icon),
                'description' => $weatherDescriptions[$code] ?? 'Unbekanntes Wetter',
            ];
        });

        return view('frontend.pages.startseite', compact('forecast'));
    }


/**
 * Testet den Heide-Park-Scraper und gibt die gescrapten Öffnungszeiten aus (nur für Debugging).
 *
 * @param ParkScraperService $scraper
 * @return \Illuminate\Http\Response
 */
public function testScraper(ParkScraperService $scraper)
{
    try {
        $result = $scraper->scrapeHeidePark();

        if (is_null($result)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Keine Öffnungszeiten konnten gescrapt werden.',
                'data' => null,
            ], 500);
        }

        // Für Debugging: Rückgabe als JSON oder formatierte Ausgabe
        return response()->json([
            'status' => 'success',
            'message' => 'Öffnungszeiten erfolgreich gescrapt.',
            'data' => $result,
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Fehler beim Testen des Heide-Park-Scrapers', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Ein Fehler ist aufgetreten: ' . $e->getMessage(),
            'data' => null,
        ], 500);
    }
}
}
