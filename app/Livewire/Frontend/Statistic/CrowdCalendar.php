<?php

namespace App\Livewire\Frontend\Statistic;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\ParkCrowdReport;
use App\Models\ParkOpeningHour;

class CrowdCalendar extends Component
{
    public $park;
    public $year;
    public $month;

    public function mount($park)
    {
        $this->park = $park;
        $this->year = now()->year;
        $this->month = now()->month;
        $this->dispatchChartUpdate();
    }

    public function prevMonth()
    {
        $date = now()->setYear($this->year)->setMonth($this->month)->subMonth();
        $this->year = $date->year;
        $this->month = $date->month;
        $this->dispatchChartUpdate();
    }

    public function nextMonth()
    {
        $date = now()->setYear($this->year)->setMonth($this->month)->addMonth();
        $this->year = $date->year;
        $this->month = $date->month;
        $this->dispatchChartUpdate();
    }

    public function dispatchChartUpdate()
    {
        $this->dispatch('updateChartMonth', $this->month, $this->year);
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

       // \Log::info('Crowd Reports (Calendar)', [
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

    public function render()
    {
        $calendar = $this->calendarData;
        $year = $this->year;
        $month = $this->month;
        return view('livewire.frontend.statistic.crowd-calendar', compact('calendar', 'year', 'month'));
    }
}
