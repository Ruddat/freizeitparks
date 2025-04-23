<?php

namespace App\Livewire\Backend\Parks;

use Carbon\Carbon;
use Livewire\Component;
use Carbon\CarbonPeriod;
use App\Models\ParkOpeningHour;

class OpeningTimesEditor extends Component
{
    public $parkId;
    public $mode = 'calendar'; // 'calendar' | 'range'

    public $opening_hours = [
        'monday' => ['open' => '', 'close' => ''],
        'tuesday' => ['open' => '', 'close' => ''],
        'wednesday' => ['open' => '', 'close' => ''],
        'thursday' => ['open' => '', 'close' => ''],
        'friday' => ['open' => '', 'close' => ''],
        'saturday' => ['open' => '', 'close' => ''],
        'sunday' => ['open' => '', 'close' => ''],
    ];

    public $defaultOpen;
    public $defaultClose;
    public $applyToAll = false;

    public $calendarMonth;
    public $calendarWeeks = [];
    public $selectedDate;
    public $calendarOpen;
    public $calendarClose;
    public $dateColors = [];
    public $dateLabels = [];

    public $rangeStart;
    public $rangeEnd;
    public $rangeOpen;
    public $rangeClose;
    public $rangeDays = [];

    public $selectedDates = [];

    public $presetTemplates = [
        'green' => ['label' => 'Sommerzeit (10–18 Uhr)', 'open' => '10:00', 'close' => '18:00'],
        'yellow' => ['label' => 'Kurzzeit (10–17 Uhr)', 'open' => '10:00', 'close' => '17:00'],
        'red' => ['label' => 'Geschlossen', 'open' => null, 'close' => null],
    ];
    public $activePreset = 'green';

    public function mount($parkId)
    {
        $this->parkId = $parkId;
        $this->calendarMonth = now()->format('Y-m');
        $this->generateCalendar();
        $this->loadOpeningHours();
    }

    public function loadOpeningHours()
    {
        $entries = ParkOpeningHour::where('park_id', $this->parkId)->get();
        foreach ($entries as $entry) {
            if ($entry->date) {
                $label = ($entry->open && $entry->close)
                    ? substr($entry->open, 0, 5) . '–' . substr($entry->close, 0, 5) . ' Uhr'
                    : 'Geschlossen';

                $color = match ([$entry->open, $entry->close]) {
                    ['10:00:00', '18:00:00'] => 'green',
                    ['10:00:00', '17:00:00'] => 'yellow',
                    [null, null] => 'red',
                    default => 'green',
                };

                $this->dateColors[$entry->date] = $color;
                $this->dateLabels[$entry->date] = $label;
            } elseif ($entry->day) {
                $this->opening_hours[$entry->day] = [
                    'open' => substr($entry->open, 0, 5),
                    'close' => substr($entry->close, 0, 5),
                ];
            }
        }
    }

    public function updatedParkId()
    {
        $this->loadOpeningHours();
        $this->generateCalendar();
    }

    public function updatedApplyToAll()
    {
        if ($this->applyToAll && $this->defaultOpen && $this->defaultClose) {
            foreach (array_keys($this->opening_hours) as $day) {
                $this->opening_hours[$day]['open'] = $this->defaultOpen;
                $this->opening_hours[$day]['close'] = $this->defaultClose;
            }
        }
    }

    public function startDrag($date)
    {
        $this->selectedDates = [$date];
    }

    public function dragOver($date)
    {
        if (!in_array($date, $this->selectedDates)) {
            $this->selectedDates[] = $date;
        }
    }

    public function stopDrag()
    {
        // Optional: could be used to finalize drag selection
    }

    public function applyPresetToSelection()
    {
        $preset = $this->presetTemplates[$this->activePreset];

        foreach ($this->selectedDates as $date) {
            ParkOpeningHour::updateOrCreate(
                ['park_id' => $this->parkId, 'date' => $date],
                ['open' => $preset['open'], 'close' => $preset['close'], 'day' => null]
            );

            $this->dateColors[$date] = $this->activePreset;
            $this->dateLabels[$date] = $preset['open'] && $preset['close']
                ? $preset['open'] . '–' . $preset['close'] . ' Uhr'
                : 'Geschlossen';
        }
    }

    public function applyRangeToCalendar()
    {
        if (!$this->rangeStart || !$this->rangeEnd || !$this->rangeOpen || !$this->rangeClose || empty($this->rangeDays)) {
            session()->flash('error', 'Bitte alle Felder und Wochentage für den Bereich ausfüllen.');
            return;
        }

        $range = CarbonPeriod::create($this->rangeStart, $this->rangeEnd);
        foreach ($range as $date) {
            $carbon = Carbon::parse($date);
            $dayName = strtolower($carbon->englishDayOfWeek); // e.g. 'monday'

            if (in_array($dayName, $this->rangeDays)) {
                ParkOpeningHour::updateOrCreate(
                    ['park_id' => $this->parkId, 'date' => $carbon->format('Y-m-d')],
                    ['open' => $this->rangeOpen, 'close' => $this->rangeClose, 'day' => null]
                );

                $this->dateColors[$carbon->format('Y-m-d')] = match([$this->rangeOpen, $this->rangeClose]) {
                    ['10:00', '18:00'] => 'green',
                    ['10:00', '17:00'] => 'yellow',
                    [null, null] => 'red',
                    default => 'green',
                };
                $this->dateLabels[$carbon->format('Y-m-d')] = $this->rangeOpen . '–' . $this->rangeClose . ' Uhr';
            }
        }

        session()->flash('success', 'Zeitraum erfolgreich angewendet.');
    }

    public function generateCalendar()
    {
        $start = Carbon::parse($this->calendarMonth)->startOfMonth()->startOfWeek();
        $end = Carbon::parse($this->calendarMonth)->endOfMonth()->endOfWeek();
        $this->calendarWeeks = collect(CarbonPeriod::create($start, $end))->chunk(7);
    }

    public function markDate($date)
    {
        $dateString = Carbon::parse($date)->format('Y-m-d');
        $preset = $this->presetTemplates[$this->activePreset];

        ParkOpeningHour::updateOrCreate(
            ['park_id' => $this->parkId, 'date' => $dateString],
            ['open' => $preset['open'], 'close' => $preset['close'], 'day' => null]
        );

        $this->dateColors[$dateString] = $this->activePreset;
        $this->dateLabels[$dateString] = $preset['open'] && $preset['close']
            ? $preset['open'] . '–' . $preset['close'] . ' Uhr'
            : 'Geschlossen';
    }

    public function previousMonth()
    {
        $this->calendarMonth = Carbon::parse($this->calendarMonth)->subMonth()->format('Y-m');
        $this->generateCalendar();
    }

    public function nextMonth()
    {
        $this->calendarMonth = Carbon::parse($this->calendarMonth)->addMonth()->format('Y-m');
        $this->generateCalendar();
    }

    public function applySummer()
    {
        $year = now()->year;
        $range = CarbonPeriod::create("{$year}-06-01", "{$year}-08-31");
        $preset = $this->presetTemplates['green'];

        foreach ($range as $date) {
            ParkOpeningHour::updateOrCreate(
                ['park_id' => $this->parkId, 'date' => $date->format('Y-m-d')],
                ['open' => $preset['open'], 'close' => $preset['close'], 'day' => null]
            );
            $this->dateColors[$date->format('Y-m-d')] = 'green';
            $this->dateLabels[$date->format('Y-m-d')] = "{$preset['open']} – {$preset['close']} Uhr";
        }
    }

    public function clearAllDates()
    {
        ParkOpeningHour::where('park_id', $this->parkId)->whereNotNull('date')->delete();
        $this->dateColors = [];
        $this->dateLabels = [];
        session()->flash('success', 'Alle tagesbasierten Öffnungszeiten wurden gelöscht.');
    }

    public function generateYear()
    {
        $year = now()->year;
        $range = CarbonPeriod::create("{$year}-01-01", "{$year}-12-31");

        foreach ($range as $date) {
            $month = $date->month;

            if (in_array($month, [1, 2, 3])) {
                // ❄️ Januar – März: geschlossen
                ParkOpeningHour::updateOrCreate(
                    ['park_id' => $this->parkId, 'date' => $date->format('Y-m-d')],
                    ['open' => null, 'close' => null, 'day' => null]
                );
                $this->dateColors[$date->format('Y-m-d')] = 'red';
                $this->dateLabels[$date->format('Y-m-d')] = 'Geschlossen';
            } else {
                // 🟢 Ab April: Sommerzeit (default)
                $preset = $this->presetTemplates['green'];
                ParkOpeningHour::updateOrCreate(
                    ['park_id' => $this->parkId, 'date' => $date->format('Y-m-d')],
                    ['open' => $preset['open'], 'close' => $preset['close'], 'day' => null]
                );
                $this->dateColors[$date->format('Y-m-d')] = 'green';
                $this->dateLabels[$date->format('Y-m-d')] = "{$preset['open']} – {$preset['close']} Uhr";
            }
        }

        session()->flash('success', 'Ganzes Jahr generiert – Januar bis März geschlossen, ab April Sommerzeit.');
    }


    public function render()
    {
        return view('livewire.backend.parks.opening-times-editor');
    }
}
