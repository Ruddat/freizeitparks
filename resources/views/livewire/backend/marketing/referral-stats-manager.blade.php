<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Referral Statistik</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">📊 Referral Statistik</h4>
                    <div class="d-flex align-items-center">
                        <div class="input-group search-area me-3">
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="🔍 Suche nach Landing Page oder Referer...">
                            <span class="input-group-text"><i class="flaticon-381-search-2"></i></span>
                        </div>
                        <div class="dropdown">
                            <select wire:model.change="perPage" class="form-select">
                                <option value="10">10 pro Seite</option>
                                <option value="25">25 pro Seite</option>
                                <option value="50">50 pro Seite</option>
                                <option value="100">100 pro Seite</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Gesamtbesuche -->
                    <div class="alert alert-info">
                        <strong>Gesamtbesuche:</strong> {{ $totalVisits }}
                    </div>

                    <!-- Statistik Blöcke -->
                    <div class="row">
                        <!-- Top Landing Pages -->
                        <div class="col-md-6 mb-4">
                            <h5>Top Landing Pages</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tbody>
                                        @foreach ($topLandingPages as $page)
                                            <tr>
                                                <td>{{ Str::limit($page->landing_page, 50) }}</td>
                                                <td><span class="badge bg-success">{{ $page->visits }} Aufrufe</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Top Referer -->
                        <div class="col-md-6 mb-4">
                            <h5>Top Referer</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tbody>
                                        @foreach ($topReferers as $ref)
                                            <tr>
                                                <td>{{ Str::limit($ref->referer_url, 50) }}</td>
                                                <td><span class="badge bg-primary">{{ $ref->count }} Einträge</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TrackVisitor Auswertung -->
                        <div class="col-md-6 mb-4">
                            <h5>TrackVisitor Auswertung</h5>
                            <p><strong>Gesamt-Sessions:</strong> {{ $totalSessions }}</p>
                            <p><strong>Ø Verweildauer (Sek.):</strong> {{ number_format($avgDwellTime, 1) }}</p>
                        </div>

                        <!-- Top Länder -->
                        <div class="col-md-6 mb-4">
                            <h5>Top Länder</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tbody>
                                        @foreach ($topCountries as $country)
                                            <tr>
                                                <td>{{ $country->country ?? 'Unbekannt' }}</td>
                                                <td><span class="badge bg-secondary">{{ $country->count }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Top Seiten nach Verweildauer -->
                        <div class="col-md-12 mb-4">
                            <h5>Top Seiten nach Verweildauer</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tbody>
                                        @foreach ($topVisitedPages as $page)
                                            <tr>
                                                <td>{{ Str::limit($page->page_url, 60) }}</td>
                                                <td>{{ $page->views }} Aufrufe</td>
                                                <td>Ø {{ number_format($page->avg_dwell, 1) }} Sek.</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Haupttabelle -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th wire:click="sortBy('landing_page')" style="cursor: pointer;">
                                        Landing Page
                                        @if ($sortField === 'landing_page')
                                            <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th wire:click="sortBy('referer_url')" style="cursor: pointer;">
                                        Referer
                                        @if ($sortField === 'referer_url')
                                            <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th wire:click="sortBy('keyword')" style="cursor: pointer;">
                                        Keyword
                                        @if ($sortField === 'keyword')
                                            <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th wire:click="sortBy('ip_address')" style="cursor: pointer;">
                                        IP
                                        @if ($sortField === 'ip_address')
                                            <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th wire:click="sortBy('visit_count')" style="cursor: pointer;">
                                        Besuche
                                        @if ($sortField === 'visit_count')
                                            <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th wire:click="sortBy('visited_at')" style="cursor: pointer;">
                                        Zuletzt besucht
                                        @if ($sortField === 'visited_at')
                                            <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th wire:click="sortBy('is_bot')" style="cursor: pointer;">
                                        Bot?
                                        @if ($sortField === 'is_bot')
                                            <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th wire:click="sortBy('country')" style="cursor: pointer;">
                                        Land
                                        @if ($sortField === 'country')
                                            <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th>Stadt</th>
                                    <th>Gerät</th>
                                    <th>OS</th>
                                    <th>Browser</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $log)
                                    <tr>
                                        <td>{{ Str::limit($log->landing_page, 50) }}</td>
                                        <td>{{ Str::limit($log->referer_url, 50) }}</td>
                                        <td>{{ Str::limit($log->keyword ?? 'N/A', 50) }}</td>
                                        <td>{{ $log->ip_address }}</td>
                                        <td>{{ $log->visit_count }}</td>
                                        <td>{{ $log->visited_at }}</td>
                                        <td>{{ $log->is_bot ? 'Ja' : 'Nein' }}</td>
                                        <td>{{ $log->country ?? '-' }}</td>
                                        <td>{{ $log->city ?? '-' }}</td>
                                        <td>{{ $log->device_type ?? '-' }}</td>
                                        <td>{{ $log->os ?? '-' }}</td>
                                        <td>{{ $log->browser ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center">Keine Daten verfügbar</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Zeige {{ $logs->firstItem() }}-{{ $logs->lastItem() }} von {{ $logs->total() }} Einträgen
                        </div>
                        {{ $logs->links('vendor.pagination.app-pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
