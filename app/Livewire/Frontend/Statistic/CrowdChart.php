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
    public $viewMode = 'day';

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

    #[On('setChartViewMode')]
    public function setChartViewMode($mode)
    {
        if (in_array($mode, ['day', 'month'])) {
            $this->viewMode = $mode;
            $this->updateChartData();
        }
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

        $queueData = \App\Models\ParkQueueTime::where('park_id', $this->park->id)
            ->whereMonth('fetched_at', $this->month)
            ->whereYear('fetched_at', $this->year)
            ->get()
            ->groupBy(fn ($entry) => Carbon::parse($entry->fetched_at)->startOfDay()->toDateString());

        return [
            'days' => $daysInMonth,
            'openings' => $openings,
            'queueData' => $queueData,
        ];
    }

    public function updateChartData()
    {
        $this->chartLabels = [];
        $this->chartData = [];

        if ($this->viewMode === 'day') {
            $entries = \App\Models\ParkQueueTime::where('park_id', $this->park->id)
                ->whereDate('fetched_at', now())
                ->get()
                ->groupBy(fn ($item) => Carbon::parse($item->fetched_at)->format('H:00'));

            $hours = collect(range(0, 23))->map(fn ($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00');

            $this->chartLabels = $hours->toArray();
            $this->chartData = $hours->map(function ($hour) use ($entries) {
                $group = $entries[$hour] ?? collect();
                $avg = $group->pluck('wait_time')->filter(fn ($v) => is_numeric($v))->avg();
                return $avg !== null ? round($avg) : 0;
            })->toArray();
        } else {
            $calendar = $this->calendarData;

            foreach ($calendar['days'] as $day) {
                $dateStr = $day->toDateString();
                $queueEntries = $calendar['queueData'][$dateStr] ?? collect();

                $validWaitTimes = $queueEntries->pluck('wait_time')->filter(fn ($v) => is_numeric($v) && $v >= 0);
                $avg = $validWaitTimes->avg();

                $this->chartLabels[] = $day->day;
                $this->chartData[] = $avg !== null ? round($avg) : 0;
            }
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
