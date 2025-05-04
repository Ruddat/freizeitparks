@extends('frontend.layouts.app')

@section('title', $park->title . ' – Statistiken')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            📊 Statistiken für {{ $park->title }}
        </h1>
        <div>
            <form method="GET" action="{{ route('parks.statistics', $park) }}" class="flex items-center">
                <label for="timeframe" class="mr-2 text-gray-700">Zeitraum:</label>
                <select name="timeframe" id="timeframe" onchange="this.form.submit()" class="border rounded-lg p-2 text-gray-700">
                    <option value="today" {{ $timeframe === 'today' ? 'selected' : '' }}>Heute</option>
                    <option value="7days" {{ $timeframe === '7days' ? 'selected' : '' }}>Letzte 7 Tage</option>
                    <option value="1month" {{ $timeframe === '1month' ? 'selected' : '' }}>Letzter Monat</option>
                    <option value="3months" {{ $timeframe === '3months' ? 'selected' : '' }}>Letzte 3 Monate</option>
                </select>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Durchschnittliche Wartezeit (Alle Attraktionen) --}}
        <div class="bg-white rounded-lg shadow-md p-6 h-[800px] flex flex-col">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">
                ⏱️ Durchschnittliche Wartezeit
            </h2>
            <div class="flex-1 min-h-0">
                <canvas id="averageWaitTimeChart"></canvas>
            </div>
        </div>

        {{-- Wartezeit-Verlauf --}}
        <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">
                📈 Verlauf der Wartezeiten ({{ $timeframe === 'today' ? 'heute' : ($timeframe === '7days' ? 'letzte 7 Tage' : ($timeframe === '1month' ? 'letzter Monat' : 'letzte 3 Monate')) }})
            </h2>
            <div class="flex-1 min-h-0">
                <canvas id="waitTimeTodayChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // ⏱️ Durchschnittliche Wartezeiten
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
                    },
                    ticks: {
                        stepSize: 5,
                        font: { size: 12 }
                    }
                },
                y: {
                    grid: { display: false },
                    ticks: {
                        font: {
                            size: 12,
                            family: 'Arial',
                        },
                        padding: 10,
                        autoSkip: false,
                        maxRotation: 0,
                        minRotation: 0,
                    }
                }
            },
            layout: {
                padding: {
                    left: 20,
                    right: 10,
                }
            }
        }
    });

    // 📈 Verlauf
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
                legend: {
                    position: 'top',
                    labels: {
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 10,
                }
            },
            scales: {
                x: {
    type: 'category', // <-- NEU!
    grid: { color: 'rgba(0, 0, 0, 0.05)' },
    title: {
        display: true,
        text: '{{ $timeframe === "today" ? "Uhrzeit" : "Datum" }}',
        font: { size: 14 }
    },
    ticks: {
        font: { size: 12 },
        callback: function(value, index, values) {
            const label = this.getLabelForValue(value);
            if ("{{ $timeframe }}" !== "today" && label.includes('-')) {
                return label.split('-').slice(1).join('-');
            }
            return label;
        }
    }
},
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
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
});
</script>
@endpush
