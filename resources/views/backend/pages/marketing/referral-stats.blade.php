@extends('backend.layouts.master')

@section('title', 'Referral Statistik')

@section('styles')
    <style>
        .card-header {
            background: linear-gradient(90deg, #4e73df, #224abe);
            color: white;
            border-radius: 8px 8px 0 0;
        }
        .stat-card {
            transition: transform 0.2s;
            border-radius: 8px;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fc;
        }
        .chart-container {
            position: relative;
            margin: auto;
            height: 300px;
            width: 100%;
        }
        .pagination .page-link {
            border-radius: 5px;
        }
    </style>
@endsection

@section('main-content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="mb-0 fw-bold">Referral Statistik</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik-Karten -->
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Gesamtbesuche</h5>
                    <p class="card-text display-4 fw-bold">{{ number_format($totalVisits) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Gesamt-Sessions</h5>
                    <p class="card-text display-4 fw-bold">{{ number_format($totalSessions) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Ø Verweildauer (Sek.)</h5>
                    <p class="card-text display-4 fw-bold">{{ number_format($avgDwellTime, 1) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Top Länder</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="topCountriesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Listen -->
    <div class="row mb-4 g-3">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Top Landing Pages</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach ($topLandingPages as $page)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ Str::limit($page->landing_page, 40) }}
                                <span class="badge bg-success rounded-pill">{{ number_format($page->visits) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Top Referer</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach ($topReferers as $ref)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ Str::limit($ref->referer_url, 40) }}
                                <span class="badge bg-primary rounded-pill">{{ number_format($ref->count) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabelle mit optimierter Pagination -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Detaillierte Logs</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <form method="GET" action="{{ route('stats-manager.referral.stats') }}" class="d-flex align-items-center">
                            <div class="input-group me-3">
                                <input type="text" name="search" class="form-control" placeholder="Suche..." value="{{ $search }}">
                                <button type="submit" class="btn btn-primary">Suchen</button>
                            </div>
                            <select name="per_page" class="form-select w-auto" onchange="this.form.submit()">
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th><a href="{{ route('stats-manager.referral.stats', array_merge(request()->query(), ['sort_field' => 'landing_page', 'sort_direction' => $sortField === 'landing_page' && $sortDirection === 'asc' ? 'desc' : 'asc'])) }}" class="text-white text-decoration-none">Landing Page @if($sortField === 'landing_page')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</a></th>
                                    <th><a href="{{ route('stats-manager.referral.stats', array_merge(request()->query(), ['sort_field' => 'referer_url', 'sort_direction' => $sortField === 'referer_url' && $sortDirection === 'asc' ? 'desc' : 'asc'])) }}" class="text-white text-decoration-none">Referer @if($sortField === 'referer_url')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</a></th>
                                    <th>Keyword</th>
                                    <th>IP</th>
                                    <th><a href="{{ route('stats-manager.referral.stats', array_merge(request()->query(), ['sort_field' => 'visit_count', 'sort_direction' => $sortField === 'visit_count' && $sortDirection === 'asc' ? 'desc' : 'asc'])) }}" class="text-white text-decoration-none">Besuche @if($sortField === 'visit_count')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</a></th>
                                    <th><a href="{{ route('stats-manager.referral.stats', array_merge(request()->query(), ['sort_field' => 'visited_at', 'sort_direction' => $sortField === 'visited_at' && $sortDirection === 'asc' ? 'desc' : 'asc'])) }}" class="text-white text-decoration-none">Zuletzt besucht @if($sortField === 'visited_at')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</a></th>
                                    <th>Land</th>
                                    <th>Browser</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $log)
                                    <tr>
                                        <td>{{ Str::limit($log->landing_page, 30) }}</td>
                                        <td>{{ Str::limit($log->referer_url, 30) }}</td>
                                        <td>{{ $log->keyword ?? 'N/A' }}</td>
                                        <td>{{ $log->ip_address }}</td>
                                        <td>{{ $log->visit_count }}</td>
                                        <td>{{ $log->visited_at }}</td>
                                        <td>{{ $log->country ?? '-' }}</td>
                                        <td>{{ $log->browser ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-3">Keine Daten verfügbar</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <div>
                            Zeige {{ $logs->firstItem() }} bis {{ $logs->lastItem() }} von {{ $logs->total() }} Einträgen
                        </div>
                        <div>
                            {{ $logs->links('vendor.pagination.ra-admin') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @php
        $countryLabels = $topCountries->pluck('country')->map(fn($c) => $c ?? 'Unbekannt')->toArray();
        $countryData = $topCountries->pluck('count')->toArray();
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('topCountriesChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($countryLabels) !!},
                    datasets: [{
                        label: 'Anzahl Besuche',
                        data: {!! json_encode($countryData) !!},
                        backgroundColor: 'rgba(78, 115, 223, 0.6)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 1,
                        borderRadius: 5,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Besuche',
                                font: { size: 14 }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Länder',
                                font: { size: 14 }
                            }
                        }
                    },
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.raw}`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

