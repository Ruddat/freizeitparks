@extends('frontend.layouts.app')

@section('title', $park->title . ' – Statistiken')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white">
                📊 Statistiken für
                <br>
                <span class="text-2xl font-semibold text-blue-200">{{ $park->name }}</span>
            </h1>

            {{-- 🏷️ Badge je nach Datenquelle --}}
            @if ($timeframe === 'today')
                <span class="inline-flex items-center mt-2 px-3 py-1 rounded-full text-sm bg-green-600 text-white">
                    🔄 Echtzeitdaten (live)
                </span>
            @else
                <span class="inline-flex items-center mt-2 px-3 py-1 rounded-full text-sm bg-yellow-600 text-white">
                    📦 Archivdaten (aggregiert)
                </span>
            @endif
        </div>

        <form method="GET" action="{{ route('parks.statistics', $park) }}" class="flex items-center text-white">
            <label for="timeframe" class="mr-2 font-medium">Zeitraum:</label>
            <select name="timeframe" id="timeframe"
                onchange="this.form.submit()"
                class="bg-white text-gray-900 border rounded-lg p-2">
                <option value="today" {{ $timeframe === 'today' ? 'selected' : '' }}>Heute</option>
                <option value="7days" {{ $timeframe === '7days' ? 'selected' : '' }}>Letzte 7 Tage</option>
                <option value="1month" {{ $timeframe === '1month' ? 'selected' : '' }}>Letzter Monat</option>
                <option value="3months" {{ $timeframe === '3months' ? 'selected' : '' }}>Letzte 3 Monate</option>
            </select>
        </form>
    </div>


    @if ($timeframe === 'today')
        <p class="text-sm text-green-600 mb-4">🔄 Datenquelle: Echtzeit-Logs (alle 15 Minuten aktualisiert)</p>
    @else
        <p class="text-sm text-blue-600 mb-4">📦 Datenquelle: Aggregierte Tageswerte aus historischen Logs</p>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- ⏱️ Durchschnittliche Wartezeit je Attraktion --}}
        <div class="bg-white rounded-lg shadow-md p-6 h-[800px] flex flex-col">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">
                ⏱️ Durchschnittliche Wartezeit je Attraktion
            </h2>
            <div class="flex-1 min-h-0">
                <canvas id="averageWaitTimeChart"></canvas>
            </div>
        </div>

        {{-- 📈 Verlauf der Wartezeiten --}}
        <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">
                📈 Verlauf der Wartezeiten ({{ $timeframe === 'today' ? 'heute' : ($timeframe === '7days' ? 'letzte 7 Tage' : ($timeframe === '1month' ? 'letzter Monat' : 'letzte 3 Monate')) }})
            </h2>
            <div class="flex-1 min-h-0">
                <canvas id="waitTimeTodayChart"></canvas>
            </div>
        </div>
    </div>

    {{-- 📅 Durchschnittliche Wartezeit pro Wochentag --}}
    @if ($weekdayAverages->isNotEmpty())
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">📅 Durchschnittliche Wartezeit pro Wochentag</h2>
            <canvas id="weekdayChart"></canvas>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // ⏱️ Durchschnittliche Wartezeiten je Attraktion
    const avgWaitCtx = document.getElementById('averageWaitTimeChart');
    new Chart(avgWaitCtx, {
        type: 'bar',
        data: {
            labels: @json($averageWaits->pluck('ride_name')),
            datasets: [{
                label: 'Ø Wartezeit (Minuten)',
                data: @json($averageWaits->pluck('avg_wait')),
                backgroundColor: 'rgba(79, 70, 229, 0.7)',
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => `${ctx.raw} Minuten`
                    },
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 10,
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                    title: {
                        display: true,
                        text: 'Minuten',
                        font: { size: 14 }
                    }
                },
                y: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 12 },
                        padding: 10,
                        autoSkip: false,
                        maxRotation: 0,
                        minRotation: 0,
                    }
                }
            }
        }
    });

    // 📈 Verlauf der Wartezeiten
    const timeChartCtx = document.getElementById('waitTimeTodayChart');
    new Chart(timeChartCtx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Ø Wartezeit (Min.)',
                data: @json($chartData),
                fill: true,
                tension: 0.3,
                borderColor: 'rgba(16, 185, 129, 1)',
                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                pointBackgroundColor: 'white',
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBorderWidth: 2,
                pointBorderColor: 'rgba(16, 185, 129, 1)',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 10,
                }
            },
            scales: {
                x: {
                    type: 'category',
                    title: {
                        display: true,
                        text: '{{ $timeframe === "today" ? "Uhrzeit" : "Datum" }}',
                        font: { size: 14 }
                    },
                    ticks: {
                        font: { size: 12 },
                        callback: function(value) {
                            const label = this.getLabelForValue(value);
                            return "{{ $timeframe }}" !== "today" && label.includes('-')
                                ? label.split('-').slice(1).join('-')
                                : label;
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Minuten',
                        font: { size: 14 }
                    },
                    ticks: {
                        stepSize: 5,
                        font: { size: 12 }
                    }
                }
            }
        }
    });

    // 📅 Durchschnittliche Wartezeit pro Wochentag
    const weekdayChart = document.getElementById('weekdayChart');
    if (weekdayChart) {
        new Chart(weekdayChart, {
            type: 'bar',
            data: {
                labels: @json($weekdayAverages->keys()),
                datasets: [{
                    label: 'Ø Wartezeit (Min.)',
                    data: @json($weekdayAverages->values()),
                    backgroundColor: 'rgba(234, 179, 8, 0.7)',
                    borderColor: 'rgba(202, 138, 4, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.raw} Minuten`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Minuten'
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
