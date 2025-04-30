<div>
    @php
        use Carbon\CarbonImmutable;

        $start = CarbonImmutable::create($year, $month, 1)->startOfWeek();
        $end = CarbonImmutable::create($year, $month, 1)->endOfMonth()->endOfWeek();
        $period = Carbon\CarbonPeriod::create($start, $end);

        $feiertage = [
            "{$year}-05-01" => 'Tag der Arbeit',
            "{$year}-05-09" => 'Christi Himmelfahrt',
            "{$year}-05-20" => 'Pfingstmontag',
            "{$year}-05-26" => 'Memorial Day',
        ];
    @endphp

    <div class="container max-w-6xl mx-auto px-4 py-6">
        <!-- Kalender-Box -->
        <div class="bg-white shadow-2xl rounded-2xl p-6 mb-8 transition-all duration-300 hover:shadow-3xl">
            <!-- Navigation -->
            <div class="flex items-center justify-between mb-6">
                <button wire:click="prevMonth" class="p-2 rounded-full bg-pink-500 hover:bg-pink-600 text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <h2 class="text-2xl font-semibold text-gray-800">
                    {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}
                </h2>
                <button wire:click="nextMonth" class="p-2 rounded-full bg-pink-500 hover:bg-pink-600 text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Legende (nur Desktop) -->
            <div class="hidden sm:flex justify-center gap-4 mb-4 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full bg-green-400"></span><span>Park</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full bg-blue-400"></span><span>Wasserpark</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full bg-red-400"></span><span>Special Event</span>
                </div>
            </div>

            <!-- Wochentage (nur Desktop) -->
            <div class="hidden sm:grid grid-cols-7 gap-1 text-center text-sm font-medium text-gray-600 mb-2">
                @foreach (['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'] as $day)
                    <div class="py-1">{{ $day }}</div>
                @endforeach
            </div>

            <!-- Kalender-Ansicht (nur Desktop) -->
            <div class="hidden sm:grid grid-cols-7 gap-1">
                @foreach ($period as $date)
                    @php
                        $isCurrentMonth = $date->month === $month;
                        $isToday = $date->isToday();
                        $isWeekend = in_array($date->dayOfWeekIso, [6,7]);
                        $dateStr = $date->toDateString();
                        $open = $calendar['openings'][$dateStr] ?? null;
                        $reports = $calendar['crowdReports'][$dateStr] ?? collect();
                        $avg = $reports->avg('crowd_level');
                        $isHoliday = array_key_exists($dateStr, $feiertage);
                        $holidayLabel = $isHoliday ? '🎉 ' . $feiertage[$dateStr] : null;

                        $parkHours = $open?->open && $open?->close ? "{$open->open}–{$open->close}" : '10:00–18:00';
                        $waterparkHours = '12:00–16:00';
                        $specialEvent = $isHoliday ? '09:00–21:00' : null;

                        $bg = 'bg-gray-100 text-gray-400';
                        if ($isHoliday) $bg = 'bg-yellow-400 text-black';
                        elseif ($avg !== null && $avg < 1.5) $bg = 'bg-green-400 text-black';
                        elseif ($avg < 2.5) $bg = 'bg-lime-300 text-black';
                        elseif ($avg < 3.5) $bg = 'bg-yellow-300 text-black';
                        elseif ($avg < 4.5) $bg = 'bg-orange-400 text-black';
                        elseif ($avg >= 4.5) $bg = 'bg-red-500 text-white';
                        if (!$isCurrentMonth) $bg = 'bg-gray-100 text-gray-400';

                        $ring = $isToday ? 'ring-2 ring-pink-500' : '';
                        $tooltip = $holidayLabel ?: ($avg ? 'Andrang: ~' . round($avg * 20) . '%' : 'Keine Daten');
                    @endphp

                    <div class="group relative border rounded-xl p-2 text-center text-sm {{ $bg }} {{ $ring }} hover:brightness-105 hover:shadow-md transition-all duration-150 cursor-pointer min-h-[120px] flex flex-col justify-between">
                        <div class="flex flex-col items-center space-y-1">
                            <span class="font-bold text-lg leading-none" title="{{ $tooltip }}">{{ $date->day }}</span>
                            @if ($holidayLabel)
                                <div class="text-xs font-semibold mt-1">{{ $holidayLabel }}</div>
                            @endif
                            @if ($isToday)
                                <div class="text-xs text-pink-600 font-semibold">Heute</div>
                            @endif
                            <div class="text-[11px] mt-1 space-y-1 text-center">
                                <div class="flex items-center gap-1 justify-center">
                                    <span class="w-2.5 h-2.5 rounded-full bg-green-400"></span>
                                    <span>{{ $parkHours }}</span>
                                </div>
                                <div class="flex items-center gap-1 justify-center">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>
                                    <span>{{ $waterparkHours }}</span>
                                </div>
                                @if ($specialEvent)
                                    <div class="flex items-center gap-1 justify-center">
                                        <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                                        <span>{{ $specialEvent }}</span>
                                    </div>
                                @endif
                                @if ($avg !== null)
                                    <div class="font-semibold mt-1">{{ round($avg * 20) }}%</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Listenansicht (nur Mobil) -->
            <div class="block sm:hidden space-y-2 mt-4">
                @foreach ($period as $date)
                    @php
                        $dateStr = $date->toDateString();
                        $isToday = $date->isToday();
                        $isCurrentMonth = $date->month === $month;
                        $open = $calendar['openings'][$dateStr] ?? null;
                        $reports = $calendar['crowdReports'][$dateStr] ?? collect();
                        $avg = $reports->avg('crowd_level');
                        $weekday = $date->translatedFormat('l');
                        $holiday = $feiertage[$dateStr] ?? null;

                        $parkHours = $open?->open && $open?->close ? "{$open->open} – {$open->close}" : '10:00 – 18:00';
                        $waterparkHours = '12:00 – 16:00';
                        $specialEvent = $holiday ? '09:00 – 21:00' : null;

                        $bg = 'bg-white text-gray-800';
                        if ($holiday) $bg = 'bg-yellow-400 text-black';
                        elseif ($avg !== null && $avg < 1.5) $bg = 'bg-green-400 text-black';
                        elseif ($avg < 2.5) $bg = 'bg-lime-300 text-black';
                        elseif ($avg < 3.5) $bg = 'bg-yellow-300 text-black';
                        elseif ($avg < 4.5) $bg = 'bg-orange-400 text-black';
                        elseif ($avg >= 4.5) $bg = 'bg-red-500 text-white';
                        if (!$isCurrentMonth) $bg = 'bg-gray-100 text-gray-400';
                    @endphp

                    <div class="rounded-lg shadow px-4 py-3 border border-gray-200 {{ $bg }}">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="font-bold text-base leading-tight">
                                    {{ $date->translatedFormat('d. F') }} ({{ $weekday }})
                                </div>
                                @if ($holiday)
                                    <div class="text-xs mt-1">🎉 {{ $holiday }}</div>
                                @endif
                                @if ($isToday)
                                    <div class="text-pink-700 text-xs font-semibold">Heute</div>
                                @endif
                            </div>
                            @if ($avg !== null)
                                <div class="text-sm font-semibold text-right">
                                    Andrang<br><span class="text-lg">{{ round($avg * 20) }}%</span>
                                </div>
                            @endif
                        </div>
                        <div class="mt-2 space-y-1 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-green-600"></span>
                                <span>Park: {{ $parkHours }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                <span>Wasserpark: {{ $waterparkHours }}</span>
                            </div>
                            @if ($specialEvent)
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                                    <span>Special Event: {{ $specialEvent }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
