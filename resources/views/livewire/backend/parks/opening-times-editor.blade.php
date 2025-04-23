<div x-data="{ dragging: false }" x-on:mouseup="$wire.call('stopDrag')">
    <!-- Navigation + Aktionen -->
    <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap">
        <div class="btn-group mb-2 mb-md-0" role="group">
            <button type="button" class="btn btn-outline-secondary" wire:click="previousMonth">⬅️</button>
            <span class="btn btn-light disabled">{{ \Carbon\Carbon::parse($calendarMonth)->translatedFormat('F Y') }}</span>
            <button type="button" class="btn btn-outline-secondary" wire:click="nextMonth">➡️</button>
        </div>
        <div class="btn-group mb-2 mb-md-0">
            <button class="btn btn-sm btn-outline-success" wire:click="applySummer">☀️ Sommerzeit (Juni–August)</button>
            <button class="btn btn-sm btn-outline-primary" wire:click="generateYear">📅 Ganzes Jahr generieren</button>
            <button class="btn btn-sm btn-outline-danger" wire:click="clearAllDates">🧹 Alles löschen</button>
        </div>
    </div>

    <!-- Modusauswahl -->
    <div class="btn-group w-100 mb-3" role="group">
        <button type="button" class="btn btn-outline-primary {{ $mode === 'week' ? 'active' : '' }}" wire:click="$set('mode', 'week')">
            📆 Wochenmodus
        </button>
        <button type="button" class="btn btn-outline-primary {{ $mode === 'calendar' ? 'active' : '' }}" wire:click="$set('mode', 'calendar')">
            📅 Kalendermodus
        </button>
    </div>

    @if ($mode === 'calendar')
        <div class="d-flex gap-2 flex-wrap mb-3">
            @foreach($presetTemplates as $key => $preset)
                <button class="btn btn-sm {{ $activePreset === $key ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="$set('activePreset', '{{ $key }}')">
                    {{ $preset['label'] }}
                </button>
            @endforeach
        </div>

        <div class="mb-3">
            <button class="btn btn-outline-success" wire:click="applyPresetToSelection">
                📌 Auf Auswahl anwenden ({{ count($selectedDates) }})
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead>
                    <tr>
                        @foreach(['Mo','Di','Mi','Do','Fr','Sa','So'] as $day)
                            <th>{{ $day }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($calendarWeeks as $week)
                        <tr>
                            @foreach ($week as $day)
                            @php
                                $dateKey = $day->format('Y-m-d');
                                $bg = match($dateColors[$dateKey] ?? null) {
                                    'green' => 'bg-success text-white',
                                    'yellow' => 'bg-warning text-dark',
                                    'red' => 'bg-danger text-white',
                                    default => ''
                                };
                                $selected = in_array($dateKey, $selectedDates)
                                    ? 'border border-3 border-dark shadow bg-opacity-25 bg-info'
                                    : '';
                                    $today = now()->format('Y-m-d') === $dateKey ? 'today-marker' : '';
                                    $tooltip = $dateLabels[$dateKey] ?? ($selected ? 'Ausgewählt – noch nicht gespeichert' : 'Keine Zeit gesetzt');
if ($today) {
    $tooltip .= ' (Heute)';
}
                            @endphp
                            <td
                                x-on:mousedown.prevent="$wire.call('startDrag', '{{ $dateKey }}'); dragging = true"
                                x-on:mouseenter="if (dragging) { $wire.call('dragOver', '{{ $dateKey }}') }"
                                x-on:mouseup="dragging = false"
                                class="calendar-day {{ $bg }} {{ $selected }} {{ $today }}"
                                title="{{ $tooltip }}"
                                style="cursor: pointer;">
                                <strong>{{ $day->format('d') }}</strong>
                                @if (isset($dateLabels[$dateKey]))
                                    <div class="small text-muted">{{ $dateLabels[$dateKey] }}</div>
                                @endif
                            </td>
                        @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="text-muted small mt-2">
            <strong>Legende:</strong>
            <span class="badge bg-success">Sommerzeit</span>
            <span class="badge bg-warning text-dark">Kurzzeit</span>
            <span class="badge bg-danger">Geschlossen</span>
            <span class="badge border border-primary text-primary">Heute</span>
        </div>
    @endif

    @if ($mode === 'week')
    <div class="card border p-3 mb-4">
        <h5>Wochentage & Zeitraum anwenden</h5>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Startdatum</label>
                <input type="date" class="form-control" wire:model.defer="rangeStart">
            </div>
            <div class="col-md-6">
                <label class="form-label">Enddatum</label>
                <input type="date" class="form-control" wire:model.defer="rangeEnd">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Öffnet</label>
                <input type="time" class="form-control" wire:model.defer="rangeOpen">
            </div>
            <div class="col-md-6">
                <label class="form-label">Schließt</label>
                <input type="time" class="form-control" wire:model.defer="rangeClose">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Auf welche Wochentage anwenden?</label>
            <div class="d-flex flex-wrap gap-3">
                @foreach(['monday'=>'Mo','tuesday'=>'Di','wednesday'=>'Mi','thursday'=>'Do','friday'=>'Fr','saturday'=>'Sa','sunday'=>'So'] as $key => $label)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" wire:model.defer="rangeDays" value="{{ $key }}" id="day_{{ $key }}">
                        <label class="form-check-label" for="day_{{ $key }}">{{ $label }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <button class="btn btn-primary w-100" wire:click="applyRangeToCalendar">⏎ Auf Zeitraum anwenden</button>
    </div>
@endif


<style>
.today-marker {
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.75);
    border-radius: 6px;
    position: relative;
}

.today-marker::after {
    content: '🟦';
    position: absolute;
    top: 2px;
    right: 4px;
    font-size: 0.65rem;
}

    .calendar-day {
        cursor: pointer;
        transition: background-color 0.3s, border 0.3s;
    }

    .calendar-day:hover {
        background-color: rgba(0, 123, 255, 0.1); /* Leicht blauer Hintergrund bei Hover */
    }
</style>
</div>
