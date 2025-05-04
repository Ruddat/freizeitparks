<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Park;
use App\Models\ParkWeather;
use App\Services\SeoService;
use Illuminate\Http\Request;
use App\Models\ParkDailyStats;
use App\Models\ParkCrowdReport;
use App\Models\ParkCrowdForecas;
use App\Models\ModVisitorSession;
use Illuminate\Support\Facades\DB;
use App\Services\NewWeatherService;
use Illuminate\Support\Facades\Http;
use App\Models\ParkQueueTimeAverages;

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

      //  dd($park->queueTimes->groupBy('ride_id')->map->count()->filter(fn($c) => $c > 1));


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
        $timeframe = $request->input('timeframe', 'today');

        $startDate = match ($timeframe) {
            '7days' => now()->subDays(7),
            '1month' => now()->subMonth(),
            '3months' => now()->subMonths(3),
            default => now()->startOfDay(),
        };

        // Datenbasis: Echtzeit (heute) oder Aggregat (älter)
        $rideStats = $timeframe === 'today'
            ? \App\Models\ParkQueueTimeLog::query()
                ->selectRaw('ride_name, ROUND(AVG(wait_time), 1) as avg_wait')
                ->where('park_id', $park->id)
                ->where('fetched_at', '>=', $startDate)
                ->groupBy('ride_name')
                ->get()
            : \App\Models\ParkDailyRideStats::query()
                ->selectRaw('ride_name, ROUND(AVG(avg_wait_time), 1) as avg_wait')
                ->where('park_id', $park->id)
                ->where('date', '>=', $startDate->toDateString())
                ->groupBy('ride_name')
                ->get();

        $allRides = $rideStats->pluck('ride_name')->unique();

        $averageWaits = $allRides->map(function ($rideName) use ($rideStats) {
            $entry = $rideStats->firstWhere('ride_name', $rideName);
            return [
                'ride_name' => $rideName,
                'avg_wait' => $entry ? $entry->avg_wait : 0,
            ];
        })->sortByDesc('avg_wait')->values();

        // Verlauf
        $waitTimelineRaw = $timeframe === 'today'
            ? \App\Models\ParkQueueTimeLog::where('park_id', $park->id)
                ->where('fetched_at', '>=', $startDate)
                ->whereNotNull('wait_time')
                ->select('fetched_at', 'wait_time')
                ->orderBy('fetched_at')
                ->get()
            : \App\Models\ParkDailyRideStats::where('park_id', $park->id)
                ->where('date', '>=', $startDate->toDateString())
                ->select('date', DB::raw('ROUND(AVG(avg_wait_time),1) as wait_time'))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(function ($item) {
                    $item->fetched_at = Carbon::parse($item->date);
                    return $item;
                });

        // Gruppieren nach Stunde/Datum
        $waitTimeline = $timeframe === 'today'
            ? $waitTimelineRaw->groupBy(fn($entry) => Carbon::parse($entry->fetched_at)->format('H:00'))
            : $waitTimelineRaw->groupBy(fn($entry) => Carbon::parse($entry->fetched_at)->format('Y-m-d'));

        $allIntervals = collect();
        if ($timeframe === 'today') {
            for ($hour = 0; $hour < 24; $hour++) {
                $label = sprintf('%02d:00', $hour);
                $allIntervals[$label] = $waitTimeline->has($label)
                    ? round($waitTimeline[$label]->avg('wait_time'), 1)
                    : 0;
            }
        } else {
            $currentDate = $startDate->copy();
            while ($currentDate <= now()) {
                $label = $currentDate->format('Y-m-d');
                $allIntervals[$label] = $waitTimeline->has($label)
                    ? round($waitTimeline[$label]->avg('wait_time'), 1)
                    : 0;
                $currentDate->addDay();
            }
        }

        $chartLabels = $allIntervals->keys();
        $chartData = $allIntervals->values();

        // Wochentag-Stats aus park_daily_stats
        $weekdayAverages = \App\Models\ParkDailyRideStats::where('park_id', $park->id)
            ->where('date', '>=', now()->subMonths(3))
            ->selectRaw('WEEKDAY(date) as weekday, ROUND(AVG(avg_wait_time),1) as avg_wait')
            ->groupBy('weekday')
            ->orderBy('weekday')
            ->get()
            ->mapWithKeys(fn($row) => [
                Carbon::create()->startOfWeek()->addDays($row->weekday)->locale('de')->isoFormat('dddd') => $row->avg_wait
            ]);

        return view('frontend.pages.park.statistics', compact(
            'park', 'averageWaits', 'chartLabels', 'chartData', 'timeframe', 'weekdayAverages'
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

        $existingRides = [];

        // 🗺️ Erst rides aus lands durchgehen
        foreach ($data['lands'] ?? [] as $land) {
            foreach ($land['rides'] ?? [] as $ride) {
                $key = $ride['id'] . '-' . $park->id;
                if (isset($existingRides[$key])) continue;

                $existingRides[$key] = true;

                $park->queueTimes()->updateOrCreate(
                    ['ride_id' => $ride['id'], 'park_id' => $park->id],
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

        // 📦 Dann rides ohne Land, aber nur wenn noch nicht verarbeitet
        foreach ($data['rides'] ?? [] as $ride) {
            $key = $ride['id'] . '-' . $park->id;
            if (isset($existingRides[$key])) continue;

            $existingRides[$key] = true;

            $park->queueTimes()->updateOrCreate(
                ['ride_id' => $ride['id'], 'park_id' => $park->id],
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
    }

}
