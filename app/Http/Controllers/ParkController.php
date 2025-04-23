<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Park;
use App\Models\ParkWeather;
use Illuminate\Http\Request;
use App\Models\ParkDailyStats;
use App\Models\ParkCrowdReport;
use App\Models\ParkCrowdForecas;
use App\Services\WeatherService;
use App\Models\ModVisitorSession;
use Illuminate\Support\Facades\Http;

class ParkController extends Controller
{
    public function show($id, WeatherService $weatherService)
    {
        $park = Park::with(['queueTimes', 'openingHours'])->findOrFail($id);

        // Öffnungszeiten & Queue aktualisieren
        $letzterEintrag = $park->queueTimes()->orderByDesc('fetched_at')->first();
        if (!$letzterEintrag || $letzterEintrag->fetched_at->lt(now()->subMinutes(10))) {
            $this->updateQueueTimesFor($park);
            $park->load('queueTimes');
        }

        // Nearby Parks
        $nearbyParks = Park::select('*')
            ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance', [
                $park->latitude,
                $park->longitude,
                $park->latitude,
            ])
            ->where('id', '!=', $park->id)
            ->having('distance', '<=', 300)
            ->orderBy('distance')
            ->limit(12)
            ->get();

        // Wetter holen
        $rawForecast = $weatherService->getForecastForCoordinates($park->latitude, $park->longitude);

        // Forecast speichern
        $today = now()->toDateString();
        foreach ($rawForecast as $day) {
            ParkWeather::updateOrCreate(
                ['park_id' => $park->id, 'date' => $day['date']],
                [
                    'temp_day' => round($day['temp_day'], 1),
                    'temp_night' => round($day['temp_night'], 1),
                    'weather_code' => $day['weather_code'],
                    'description' => $weatherDescriptions[$day['weather_code']] ?? null,
                    'icon' => $weatherIcons[$day['weather_code']]['day'] ?? null,
                    'fetched_at' => now(),
                ]
            );
        }

        // Wetterdaten für die nächsten 7 Tage abrufen
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
                'date'        => \Carbon\Carbon::parse($item['date'])->translatedFormat('D d.m.'),
                'temp_day'    => round($item['temp_day']),
                'temp_night'  => round($item['temp_night']),
                'icon'        => asset('images/weather/animated/' . $icon),
                'description' => $weatherDescriptions[$code] ?? 'Unbekannt',
            ];
        });

        // 🆕 Automatischer Crowd-Report, falls noch keine Bewertung abgegeben wurde
        $hasTodayCrowdReport = ParkCrowdReport::where('park_id', $park->id)
            ->whereDate('created_at', $today)
            ->exists();

        if (!$hasTodayCrowdReport) {
            ParkCrowdReport::create([
                'park_id' => $park->id,
                'crowd_level' => 2, // niedriger Grundwert als Besucher-Anwesenheitsindikator
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Tageswerte aggregieren
        $avgTempDay = ParkWeather::where('park_id', $park->id)->where('date', $today)->avg('temp_day');
        $avgTempNight = ParkWeather::where('park_id', $park->id)->where('date', $today)->avg('temp_night');
        $forecastCrowd = ParkCrowdForecas::where('park_id', $park->id)->where('date', $today)->avg('crowd_level');
        $userReportedCrowd = ParkCrowdReport::where('park_id', $park->id)->whereDate('created_at', $today)->avg('crowd_level');

        if ($forecastCrowd && $userReportedCrowd) {
            $avgCrowd = round(($forecastCrowd + $userReportedCrowd) / 2);
        } elseif ($forecastCrowd) {
            $avgCrowd = round($forecastCrowd);
        } elseif ($userReportedCrowd) {
            $avgCrowd = round($userReportedCrowd);
        } else {
            $avgCrowd = null;
        }

        $weatherCode = ParkWeather::where('park_id', $park->id)->where('date', $today)
            ->select('weather_code')
            ->groupBy('weather_code')
            ->orderByRaw('COUNT(*) DESC')
            ->value('weather_code');

        ParkDailyStats::updateOrCreate(
            ['park_id' => $park->id, 'date' => $today],
            [
                'avg_temp_day' => $avgTempDay,
                'avg_temp_night' => $avgTempNight,
                'avg_crowd_level' => $avgCrowd,
                'weather_code' => $weatherCode,
                'description' => $weatherDescriptions[$weatherCode] ?? null,
            ]
        );

        $cookieName = 'hideCrowdModal_' . $park->id;
        $cookieBlock = request()->cookie($cookieName);
        $showCrowdModal = !$hasTodayCrowdReport && !$cookieBlock;


        $visits24h = ModVisitorSession::where('page_url', 'LIKE', '%/parks/' . $park->id . '%')
        ->where('last_activity_at', '>=', now()->subHours(24))
        ->count();
//dd($visits24h);

        return view('frontend.pages.park_details', compact(
            'park',
            'nearbyParks',
            'forecast',
            'showCrowdModal',
            'visits24h'
        ));

    }


    protected function updateQueueTimesFor(Park $park): void
    {
        if (!$park->queue_times_id) return;

        $url = "https://queue-times.com/parks/{$park->queue_times_id}/queue_times.json";
        $response = Http::timeout(10)->get($url);

        if (!$response->successful()) return;

        $data = $response->json();
        $now = now();

        foreach ($data['rides'] ?? [] as $ride) {
            $park->queueTimes()->updateOrCreate(
                ['ride_id' => $ride['id']],
                [
                    'ride_name'    => $ride['name'],
                    'is_open'      => $ride['is_open'],
                    'wait_time'    => $ride['wait_time'],
                    'last_updated' => Carbon::parse($ride['last_updated']),
                    'land_name'    => null,
                    'fetched_at'   => $now,
                ]
            );
        }

        foreach ($data['lands'] ?? [] as $land) {
            foreach ($land['rides'] ?? [] as $ride) {
                $park->queueTimes()->updateOrCreate(
                    ['ride_id' => $ride['id']],
                    [
                        'ride_name'    => $ride['name'],
                        'is_open'      => $ride['is_open'],
                        'wait_time'    => $ride['wait_time'],
                        'last_updated' => Carbon::parse($ride['last_updated']),
                        'land_name'    => $land['name'],
                        'fetched_at'   => $now,
                    ]
                );
            }
        }
    }


}
