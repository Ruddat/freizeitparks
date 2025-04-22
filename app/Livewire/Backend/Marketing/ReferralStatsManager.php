<?php

namespace App\Livewire\Backend\Marketing;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ModReferralLog;
use App\Models\ModVisitorSession;
use Illuminate\Support\Facades\DB;

class ReferralStatsManager extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'visited_at'; // Standard-Sortierfeld
    public $sortDirection = 'desc';   // Standard-Sortierreihenfolge

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Methode zum Umschalten der Sortierung
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function render()
    {
        $logs = ModReferralLog::query()
        ->when($this->search, fn ($q) =>
            $q->where('landing_page', 'like', '%' . $this->search . '%')
              ->orWhere('referer_url', 'like', '%' . $this->search . '%')
              ->orWhere('country', 'like', '%' . $this->search . '%')
              ->orWhere('city', 'like', '%' . $this->search . '%')
              ->orWhere('browser', 'like', '%' . $this->search . '%')
              ->orWhere('os', 'like', '%' . $this->search . '%')
        )
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate($this->perPage);

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

        // Top 5 Länder
        $topCountries = ModReferralLog::select('country', DB::raw('COUNT(*) as count'))
        ->groupBy('country')
        ->orderByDesc('count')
        ->take(5)
        ->get();

        $totalSessions = ModVisitorSession::count();
        $avgDwellTime = ModVisitorSession::avg('dwell_time');

        return view('livewire.backend.marketing.referral-stats-manager', compact(
            'logs',
            'topLandingPages',
            'topReferers',
            'totalVisits',
            'topVisitedPages',
            'totalSessions',
            'avgDwellTime',
            'topCountries'
        ))->layout('backend.layouts.backend');
    }
}
