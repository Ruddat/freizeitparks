<?php

namespace App\Livewire\Frontend\Statistic;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\ParkCrowdReport;
use App\Models\ParkOpeningHour;

class CrowdChart extends Component
{
    public $park;
    public $year;
    public $month;
    public $chartLabels = [];
    public $chartData = [];

    public function mount($park, $year, $month)
    {
        $this->park = $park;
        $this->year = $year;
        $this->month = $month;
        $this->updateChartData();
    }

    #[On('updateChartMonth')]
    public function updateChartMonth($month, $year)
    {
     //   \Log::info('updateChartMonth Event empfangen', [
     //       'month' => $month,
     //       'year' => $year,
     //   ]);

        $this->month = $month;
        $this->year = $year;
        $this->updateChartData();
    }

    public function getCalendarDataProperty()
    {
        $daysInMonth = collect(range(1, Carbon::create($this->year, $this->month)->daysInMonth))->map(function ($day) {
            return Carbon::create($this->year, $this->month, $day);
        });

        $openings = ParkOpeningHour::where('park_id', $this->park->id)
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->get()
            ->keyBy('date');

        $crowdReports = ParkCrowdReport::where('park_id', $this->park->id)
            ->whereMonth('created_at', $this->month)
            ->whereYear('created_at', $this->year)
            ->get()
            ->groupBy(function ($report) {
                return Carbon::parse($report->created_at)->setTimezone('UTC')->startOfDay()->toDateString();
            });

       // \Log::info('Crowd Reports (Chart)', [
       //     'park_id' => $this->park->id,
       //     'month' => $this->month,
       //     'year' => $this->year,
       //     'crowdReports' => $crowdReports->toArray(),
       // ]);

        return [
            'days' => $daysInMonth,
            'openings' => $openings,
            'crowdReports' => $crowdReports,
        ];
    }

    public function updateChartData()
    {
        $calendar = $this->calendarData;

        $this->chartLabels = [];
        $this->chartData = [];
        foreach ($calendar['days'] as $day) {
            $dateStr = $day->toDateString();
            $reports = $calendar['crowdReports'][$dateStr] ?? collect();

            $validReports = $reports->filter(function ($report) {
                return !is_null($report->crowd_level);
            });

            $avg = $validReports->avg('crowd_level');

          //  \Log::info("Chart Datum: $dateStr", [
          //      'reports' => $reports->toArray(),
          //      'valid_reports' => $validReports->toArray(),
          //      'avg' => $avg,
          //      'calculated_value' => $avg !== null ? round($avg * 33) : 0,
          //  ]);

            $this->chartLabels[] = $day->day;
            $this->chartData[] = $avg !== null ? round($avg * 20) : 0;
        }
    }

    public function render()
    {
        return view('livewire.frontend.statistic.crowd-chart', [
            'year' => $this->year,
            'month' => $this->month,
        ]);
    }
}
