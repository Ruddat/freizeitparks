<?php

namespace App\Http\Controllers\Backend\Marketing;

use Illuminate\Http\Request;
use App\Models\ModReferralLog;
use App\Models\ModVisitorSession;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ReferralStatsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 10);
        $sortField = $request->input('sort_field', 'visited_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        // Referral Logs mit Suche, Sortierung und Pagination
        $logsQuery = ModReferralLog::query()
            ->when($search, fn ($q) =>
                $q->where('landing_page', 'like', '%' . $search . '%')
                  ->orWhere('referer_url', 'like', '%' . $search . '%')
                  ->orWhere('country', 'like', '%' . $search . '%')
                  ->orWhere('city', 'like', '%' . $search . '%')
                  ->orWhere('browser', 'like', '%' . $search . '%')
                  ->orWhere('os', 'like', '%' . $search . '%')
            )
            ->orderBy($sortField, $sortDirection);

        $logs = $logsQuery->Paginate($perPage)->appends($request->query());

        // Statistik-Daten (kompakte Übersicht)
        $topLandingPages = ModReferralLog::select('landing_page', DB::raw('SUM(visit_count) as visits'))
            ->groupBy('landing_page')
            ->orderByDesc('visits')
            ->take(5)
            ->get();

        $topReferers = ModReferralLog::select('referer_url', DB::raw('COUNT(*) as count'))
            ->groupBy('referer_url')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        $totalVisits = ModReferralLog::sum('visit_count');

        $topVisitedPages = ModVisitorSession::select('page_url', DB::raw('AVG(dwell_time) as avg_dwell'), DB::raw('COUNT(*) as views'))
            ->groupBy('page_url')
            ->orderByDesc('views')
            ->take(5)
            ->get();

        $topCountries = ModReferralLog::select('country', DB::raw('COUNT(*) as count'))
            ->groupBy('country')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        $totalSessions = ModVisitorSession::count();
        $avgDwellTime = ModVisitorSession::avg('dwell_time');

        return view('backend.pages.marketing.referral-stats', compact(
            'logs',
            'topLandingPages',
            'topReferers',
            'totalVisits',
            'topVisitedPages',
            'totalSessions',
            'avgDwellTime',
            'topCountries',
            'search',
            'perPage',
            'sortField',
            'sortDirection'
        ));
    }

    public function getTopCountries()
    {
        $topCountries = ModReferralLog::select('country', DB::raw('COUNT(*) as count'))
            ->groupBy('country')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        return response()->json($topCountries);
    }
}
