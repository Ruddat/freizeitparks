<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Park;
use App\Models\ParkWeather;
use App\Models\ParkDailyStats;
use App\Models\ParkCrowdReport;
use App\Models\ParkCrowdForecas;
use App\Models\ModVisitorSession;
use App\Services\SeoService;
use App\Services\NewWeatherService;
use Illuminate\Support\Facades\Http;

class ParkController extends Controller
{
    public function show(string $identifier, NewWeatherService $weatherService)
    {
        $park = Park::with(['queueTimes', 'openingHours'])
            ->where('slug', $identifier)
            ->orWhere('id', $identifier)
            ->firstOrFail();

        if (is_numeric($identifier) && $park->slug) {
            return redirect()->route('parks.show', $park->slug);
        }

        $letzterEintrag = $park->queueTimes()->orderByDesc('fetched_at')->first();
        if (!$letzterEintrag || $letzterEintrag->fetched_at->lt(now()->subMinutes(10))) {
            $this->updateQueueTimesFor($park);
            $park->load('queueTimes');
        }

        $nearbyParks = Park::select('*')
            ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance', [
                $park->latitude,
                $park->longitude,
                $park->latitude,
            ])
            ->where('id', '!=', $park->id)
            ->where('status', 'active')
            ->having('distance', '<=', 300)
            ->orderBy('distance')
            ->limit(12)
            ->get();

        $rawForecast = cache()->remember('park_forecast_' . $park->id, 3600, function () use ($weatherService, $park) {
            return $weatherService->getSevenDayForecast($park->latitude, $park->longitude, $park->id);
        });

        $today = now()->toDateString();

        foreach ($rawForecast as $day) {
            ParkWeather::updateOrCreate(
                ['park_id' => $park->id, 'date' => Carbon::createFromFormat('D, d.m.', $day['date'])->format('Y-m-d')],
                [
                    'temp_day'     => $day['temp_day'],
                    'temp_night'   => $day['temp_night'],
                    'weather_code' => $day['weather_code'],
                    'description'  => $day['description'],
                    'icon'         => $day['icon'],
                    'wind_speed'   => $day['wind_speed'],
                    'uv_index'     => $day['uv_index'],
                    'rain_chance'  => $day['rain_chance'],
                    'fetched_at'   => now(),
                ]
            );
        }

        $forecast = collect($rawForecast)->map(fn($item) => [
            'date'        => $item['date'],
            'temp_day'    => $item['temp_day'],
            'temp_night'  => $item['temp_night'],
            'weather_code'=> $item['weather_code'],
            'icon'        => asset('images/weather/lottie/' . $item['icon']),
            'description' => $item['description'],
            'wind_speed'  => $item['wind_speed'],
            'uv_index'    => $item['uv_index'],
            'rain_chance' => $item['rain_chance'],
        ]);

        $hasTodayCrowdReport = ParkCrowdReport::where('park_id', $park->id)
            ->whereDate('created_at', $today)
            ->exists();

        if (!$hasTodayCrowdReport) {
            ParkCrowdReport::create([
                'park_id' => $park->id,
                'crowd_level' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $avgTempDay = ParkWeather::where('park_id', $park->id)->where('date', $today)->avg('temp_day');
        $avgTempNight = ParkWeather::where('park_id', $park->id)->where('date', $today)->avg('temp_night');
        $forecastCrowd = ParkCrowdForecas::where('park_id', $park->id)->where('date', $today)->avg('crowd_level');
        $userReportedCrowd = ParkCrowdReport::where('park_id', $park->id)->whereDate('created_at', $today)->avg('crowd_level');

        $avgCrowd = match (true) {
            $forecastCrowd && $userReportedCrowd => round(($forecastCrowd + $userReportedCrowd) / 2),
            $forecastCrowd                       => round($forecastCrowd),
            $userReportedCrowd                   => round($userReportedCrowd),
            default                              => null,
        };

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
                'description' => ParkWeather::where('park_id', $park->id)->where('date', $today)->value('description'),
            ]
        );

        $cookieName = 'hideCrowdModal_' . $park->id;
        $cookieBlock = request()->cookie($cookieName);
        $showCrowdModal = !$hasTodayCrowdReport && !$cookieBlock;

        $visits24h = ModVisitorSession::where('page_url', 'LIKE', '%/parks/' . $park->id . '%')
            ->where('last_activity_at', '>=', now()->subHours(24))
            ->count();

        $seo = app(SeoService::class)->getSeoData($park);

        return view('frontend.pages.park_details', compact(
            'park', 'nearbyParks', 'forecast', 'showCrowdModal', 'visits24h', 'seo'
        ));
    }

    public function summary(Park $park)
    {
        return view('parks.summary', compact('park'));
    }

    public function calendar(Park $park)
    {

        return view('frontend.pages.park-crowd-calender', compact('park'));
    }

    public function statistics(Request $request, Park $park)
    {
        // Zeitraum aus dem Request holen (Standard: "today")
        $timeframe = $request->input('timeframe', 'today');

        // Datum basierend auf dem Zeitraum berechnen
        $startDate = now();
        if ($timeframe === '7days') {
            $startDate = now()->subDays(7);
        } elseif ($timeframe === '1month') {
            $startDate = now()->subMonth();
        } elseif ($timeframe === '3months') {
            $startDate = now()->subMonths(3);
        } else {
            $timeframe = 'today'; // Standard: Heute
            $startDate = now()->startOfDay();
        }

        // Hole alle eindeutigen Attraktionen aus der park_queue_times-Tabelle für diesen Park
        $allRides = $park->queueTimes()
            ->distinct()
            ->pluck('ride_name');

        // ⏱️ Durchschnittliche Wartezeit pro Attraktion (alle Attraktionen, auch mit 0)
        $waitTimesQuery = $park->queueTimes()
            ->whereNotNull('wait_time')
            ->where('fetched_at', '>=', $startDate)
            ->select('ride_name')
            ->selectRaw('ROUND(AVG(wait_time), 1) as avg_wait')
            ->groupBy('ride_name')
            ->orderByDesc('avg_wait');

        $waitTimes = $waitTimesQuery->get()->keyBy('ride_name');

        // Erstelle eine Liste mit allen Attraktionen, auch solchen ohne Wartezeit
        $averageWaits = $allRides->map(function ($rideName) use ($waitTimes) {
            return [
                'ride_name' => $rideName,
                'avg_wait' => $waitTimes->has($rideName) ? $waitTimes[$rideName]->avg_wait : 0,
            ];
        })->sortByDesc('avg_wait')->values();

        // 📈 Verlauf der Wartezeiten
        $waitTimelineRaw = $park->queueTimes()
            ->where('fetched_at', '>=', $startDate)
            ->whereNotNull('wait_time')
            ->select('fetched_at', 'wait_time')
            ->orderBy('fetched_at')
            ->get();

        // Gruppierung basierend auf dem Zeitraum
        if ($timeframe === 'today') {
            // Pro Stunde für "Heute"
            $waitTimeline = $waitTimelineRaw
                ->groupBy(fn($entry) => $entry->fetched_at->format('H:00'));

            $allIntervals = collect();
            for ($hour = 0; $hour < 24; $hour++) {
                $hourLabel = sprintf('%02d:00', $hour);
                $allIntervals[$hourLabel] = $waitTimeline->has($hourLabel)
                    ? round($waitTimeline[$hourLabel]->avg('wait_time'), 1)
                    : 0;
            }
        } else {
            // Pro Tag für längere Zeiträume
            $waitTimeline = $waitTimelineRaw
                ->groupBy(fn($entry) => $entry->fetched_at->format('Y-m-d'));

            $allIntervals = collect();
            $currentDate = $startDate->copy();
            while ($currentDate <= now()) {
                $dateLabel = $currentDate->format('Y-m-d');
                $allIntervals[$dateLabel] = $waitTimeline->has($dateLabel)
                    ? round($waitTimeline[$dateLabel]->avg('wait_time'), 1)
                    : 0;
                $currentDate->addDay();
            }
        }

        $chartLabels = $allIntervals->keys();
        $chartData = $allIntervals->values();

        // Debugging
        // dd($averageWaits, $chartLabels, $chartData);

        return view('frontend.pages.park.statistics', compact('park', 'averageWaits', 'chartLabels', 'chartData', 'timeframe'));
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
