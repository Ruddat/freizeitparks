<?php

namespace App\Http\Controllers\Frontend;

use Carbon\Carbon;
use App\Models\Park;
use App\Services\SeoService;
use Illuminate\Http\Request;
use App\Services\NewWeatherService;
use App\Http\Controllers\Controller;
use App\Services\ParkScraperService;
use App\Models\ParkWeather;

class IndexController extends Controller
{
    protected NewWeatherService $weatherService;

    public function __construct(NewWeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function index(SeoService $seoService)
    {
        $seo = $seoService->getDefaultSeoForStartpage();

        $parkId = 133;

        $rawForecast = cache()->remember("weather_forecast_park_{$parkId}", 60 * 60, function () use ($parkId) {
            return $this->weatherService->getSevenDayForecast(52.5200, 13.4050, $parkId);
        });

//dd($rawForecast);

if (empty($rawForecast)) {
    $rawForecast = ParkWeather::where('park_id', $parkId)
        ->where('date', '>=', now()->startOfDay())
        ->orderBy('date')
        ->take(7)
        ->get()
        ->map(function ($item) {
            return [
                'date' => Carbon::parse($item->date)->format('D, d.m.'),
                'temp_day' => round($item->temp_day),
                'temp_night' => round($item->temp_night),
                'weather_code' => $item->weather_code,
                'description' => $item->description ?? 'Unbekannt',
                'icon' => $item->icon ?? 'not-available.json',
                'wind_speed' => $item->wind_speed ?? null,
                'uv_index' => $item->uv_index ?? null,
                'rain_chance' => $item->rain_chance ?? null,
            ];
        })->toArray();
}

$forecast = collect($rawForecast)->map(function ($item) {
    return [
        'date' => $item['date'],
        'temp_day' => $item['temp_day'],
        'temp_night' => $item['temp_night'],
        'weather_code' => $item['weather_code'],
        'icon' => asset('images/weather/lottie/' . $item['icon']),
        'description' => $item['description'],
        'wind_speed' => $item['wind_speed'] ?? null,
        'uv_index' => $item['uv_index'] ?? null,
        'rain_chance' => $item['rain_chance'] ?? null,
    ];
});

        return view('frontend.pages.startseite', compact('forecast', 'seo'));
    }

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
