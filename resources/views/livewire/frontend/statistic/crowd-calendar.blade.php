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
        <!-- Kalender -->
        <div class="bg-white shadow-2xl rounded-2xl p-6 mb-8 transition-all duration-300 hover:shadow-3xl">
            <!-- Navigation -->
            <div class="flex items-center justify-between mb-6">
                <button wire:click="prevMonth" class="p-2 rounded-full bg-pink-500 hover:bg-pink-600 text-white transition-all duration-200 transform hover:scale-105">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <h2 class="text-2xl font-semibold text-gray-800">
                    {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}
                </h2>
                <button wire:click="nextMonth" class="p-2 rounded-full bg-pink-500 hover:bg-pink-600 text-white transition-all duration-200 transform hover:scale-105">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Legende -->
            <div class="flex justify-center gap-4 mb-4 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full bg-green-400"></span>
                    <span>Park</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full bg-blue-400"></span>
                    <span>Wasserpark</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full bg-red-400"></span>
                    <span>Special Event</span>
                </div>
            </div>

            <!-- Wochentage -->
            <div class="grid grid-cols-7 gap-1 text-center text-sm font-medium text-gray-600 mb-2">
                @foreach (['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'] as $day)
                    <div class="py-1">{{ $day }}</div>
                @endforeach
            </div>

            <!-- Tage -->
            <div class="grid grid-cols-7 gap-1">
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
                        $waterparkHours = $open?->open && $open?->close ? '12:00–16:00' : '12:00–16:00';
                        $specialEvent = $isHoliday ? '09:00–21:00' : null;

                        $bg = !$isCurrentMonth ? 'bg-gray-100 text-gray-400 border-gray-200' : (
                            $isHoliday ? 'bg-yellow-400 text-black border-yellow-500' : (
                                $avg === null ? 'bg-gray-50 text-gray-700 border-gray-200' : (
                                    $avg < 1.5 ? 'bg-green-400 text-black border-green-500' : (
                                        $avg < 2.5 ? 'bg-yellow-300 text-black border-yellow-400' : 'bg-red-400 text-white border-red-500'
                                    )
                                )
                            )
                        );

                        $todayRing = $isToday ? 'ring-2 ring-pink-500' : '';
                        $weekendBg = $isWeekend && $isCurrentMonth && !$isHoliday && $avg === null ? 'bg-slate-100' : '';

                        $tooltip = $holidayLabel ?: ($avg ? 'Andrang: ~' . round($avg * 33) . '%' : 'Keine Daten');
                    @endphp

                    <div class="group relative border rounded-xl p-2 text-center text-sm {{ $bg }} {{ $todayRing }} {{ $weekendBg }} hover:brightness-105 hover:shadow-md transition-all duration-150 cursor-pointer"
                         data-tooltip-target="tooltip-{{ $dateStr }}">
                        <div class="flex flex-col items-center justify-between">
                            <span class="font-bold text-lg">{{ $date->day }}</span>

                            @if ($isToday)
                                <div class="text-xs text-pink-600 font-semibold mt-1">Heute</div>
                            @endif

                            @if ($isCurrentMonth)
                                <div class="text-xs mt-1">
                                    <div class="flex items-center gap-1 justify-center">
                                        <span class="w-3 h-3 rounded-full bg-green-400"></span>
                                        <span>{{ $parkHours }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 justify-center">
                                        <span class="w-3 h-3 rounded-full bg-blue-400"></span>
                                        <span>{{ $waterparkHours }}</span>
                                    </div>
                                    @if ($specialEvent)
                                        <div class="flex items-center gap-1 justify-center">
                                            <span class="w-3 h-3 rounded-full bg-red-400"></span>
                                            <span>{{ $specialEvent }}</span>
                                        </div>
                                    @endif
                                    @if ($avg !== null)
                                        <div class="text-xs font-semibold mt-1">{{ round($avg * 33) }}%</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <!-- Tooltip -->
                        <div id="tooltip-{{ $dateStr }}"
                             class="absolute invisible group-hover:visible z-20 bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-2 text-sm text-white bg-gray-900 rounded-lg shadow-xl opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            {{ $tooltip }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <style>
            @media (max-width: 640px) {
                .grid-cols-7 {
                    display: flex;
                    flex-wrap: nowrap;
                    overflow-x-auto;
                    gap: 0.25rem;
                }
                .grid-cols-7 > div {
                    flex: 0 0 100px;
                    min-height: 120px;
                }
                .grid-cols-7 > div > div {
                    height: auto;
                }
            }
        </style>
    </div>
</div>
