@extends('frontend.layouts.app')

@section('title', $park->title . ' – Übersicht')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">
        📊 Übersicht – {{ $park->name }}
    </h1>

    {{-- Verlaufsdiagramm --}}
    <div class="bg-white rounded-xl shadow p-6 mb-10 h-[400px]">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Verlauf der letzten 30 Tage</h2>
        <canvas id="parkSummaryChart" class="w-full h-[300px]"></canvas>
    </div>

    {{-- Top-Kacheln --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        {{-- Sonnigste Tage --}}
        <div class="bg-yellow-100 p-4 rounded-lg shadow">
            <h3 class="font-semibold text-yellow-800 mb-2">☀️ Sonnigste Tage</h3>
            <ul class="text-sm text-yellow-900 space-y-1">
                @foreach ($sunnyDays as $day)
                    <li>{{ $day->date }} – {{ round($day->sunshine_duration / 3600, 1) }}h Sonne</li>
                @endforeach
            </ul>
        </div>

        {{-- Regnerischste Tage --}}
        <div class="bg-blue-100 p-4 rounded-lg shadow">
            <h3 class="font-semibold text-blue-800 mb-2">🌧️ Regnerischste Tage</h3>
            <ul class="text-sm text-blue-900 space-y-1">
                @foreach ($rainyDays as $day)
                    <li>{{ $day->date }} – {{ $day->precipitation_sum }} mm</li>
                @endforeach
            </ul>
        </div>

        {{-- Vollste Tage --}}
        <div class="bg-red-100 p-4 rounded-lg shadow">
            <h3 class="font-semibold text-red-800 mb-2">🧍‍♂️ Vollste Tage</h3>
            <ul class="text-sm text-red-900 space-y-1">
                @foreach ($crowdedDays as $day)
                    <li>{{ $day->date }} – Level {{ $day->avg_crowd_level }}</li>
                @endforeach
            </ul>
        </div>

        {{-- Leerste Tage --}}
        <div class="bg-green-100 p-4 rounded-lg shadow">
            <h3 class="font-semibold text-green-800 mb-2">🪑 Leerste Tage</h3>
            <ul class="text-sm text-green-900 space-y-1">
                @foreach ($emptyDays as $day)
                    <li>{{ $day->date }} – Level {{ $day->avg_crowd_level }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('parkSummaryChart');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($days->pluck('date')),
            datasets: [
                {
                    label: 'Ø Wartezeit',
                    data: @json($days->pluck('avg_wait_time')),
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.3,
                    yAxisID: 'y',
                },
                {
                    label: 'Sonnenschein (h)',
                    data: @json($days->pluck('sunshine_duration')->map(fn($v) => round($v / 3600, 1))),
                    borderColor: 'rgba(234, 179, 8, 1)',
                    backgroundColor: 'rgba(234, 179, 8, 0.2)',
                    tension: 0.3,
                    yAxisID: 'y1',
                },
                {
                    label: 'Regen (mm)',
                    data: @json($days->pluck('precipitation_sum')),
                    borderColor: 'rgba(96, 165, 250, 1)',
                    backgroundColor: 'rgba(96, 165, 250, 0.2)',
                    tension: 0.3,
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            stacked: false,
            scales: {
                y: {
                    type: 'linear',
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Wartezeit (Min.)'
                    }
                },
                y1: {
                    type: 'linear',
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Sonne / Regen'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
});
</script>
@endpush
