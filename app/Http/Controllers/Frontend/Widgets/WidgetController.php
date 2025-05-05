<?php

namespace App\Http\Controllers\Frontend\Widgets;

use App\Models\Park;
use App\Models\ParkWeather;
use Illuminate\Http\Request;
use App\Models\ParkDailyStats;
use App\Models\ParkQueueTimeLog;
use App\Http\Controllers\Controller;

class WidgetController extends Controller
{
    public function trend($identifier)
    {
        $park = Park::where('slug', $identifier)->orWhere('id', $identifier)->firstOrFail();
        $today = now()->toDateString();

        $weather = ParkWeather::where('park_id', $park->id)->where('date', $today)->first();
        $stats = ParkDailyStats::where('park_id', $park->id)->where('date', $today)->first();

        $avgWait = ParkQueueTimeLog::where('park_id', $park->id)
            ->whereDate('fetched_at', $today)
            ->avg('wait_time');

        return view('frontend.pages.widgets.trend', compact('park', 'weather', 'avgWait', 'stats'));
    }

    public function overview()
    {
        $parks = \App\Models\Park::where('status', 'active')
            ->whereNotNull('queue_times_id') // optional: nur Parks mit Daten
            ->orderBy('name')
            ->paginate(12);

        return view('frontend.pages.widgets.overview', compact('parks'));
    }
}
