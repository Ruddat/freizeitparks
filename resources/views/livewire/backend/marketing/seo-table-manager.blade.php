
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">SEO-Verwaltung</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">🔍 SEO-Einträge</h4>
                    <div class="input-group search-area w-auto" style="max-width: 500px;">
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Suche...">
                        <span class="input-group-text"><i class="flaticon-381-search-2"></i></span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered text-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="cursor-pointer" wire:click="sortBy('id')">
                                        ID @if($sortField === 'id') <span class="text-muted">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                                    </th>
                                    <th class="cursor-pointer" wire:click="sortBy('model_type')">
                                        <i class="ti ti-database"></i> Model @if($sortField === 'model_type') <span class="text-muted">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                                    </th>
                                    <th class="cursor-pointer" wire:click="sortBy('model_id')">
                                        ID @if($sortField === 'model_id') <span class="text-muted">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                                    </th>
                                    <th class="cursor-pointer" wire:click="sortBy('title')">
                                        Titel @if($sortField === 'title') <span class="text-muted">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                                    </th>
                                    <th>OG:Bild</th>
                                    <th class="cursor-pointer" wire:click="sortBy('prevent_override')">
                                        Überschreiben @if($sortField === 'prevent_override') <span class="text-muted">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                                    </th>
                                    <th class="cursor-pointer" wire:click="sortBy('updated_at')">
                                        Bearbeitet @if($sortField === 'updated_at') <span class="text-muted">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                                    </th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($seoMetas as $seoMeta)
                                    <tr>
                                        <td>{{ $seoMeta->id }}</td>
                                        <td>
                                            @php
                                                $icon = 'ti-file';
                                                if (str_contains($seoMeta->model_type, 'StaticPage')) $icon = 'ti-file-text';
                                                if (str_contains($seoMeta->model_type, 'Park')) $icon = 'ti-map-pin';
                                                if ($seoMeta->model_type === 'startpage') $icon = 'ti-home';
                                            @endphp
                                            <i class="ti {{ $icon }} text-muted me-1"></i>
                                            {{ class_basename($seoMeta->model_type) }}
                                        </td>
                                        <td>{{ $seoMeta->model_id }}</td>
                                        <td class="text-truncate" style="max-width: 300px;" title="{{ $seoMeta->title }}">{{ $seoMeta->title }}</td>
                                        <td>
                                            @if($seoMeta->image)
                                                <img src="{{ $seoMeta->image }}" alt="preview" style="max-height: 40px;">
                                            @else
                                                <span class="text-muted">–</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button wire:click="togglePreventOverride({{ $seoMeta->id }})" class="btn btn-sm {{ $seoMeta->prevent_override ? 'btn-danger' : 'btn-success' }}">
                                                {{ $seoMeta->prevent_override ? 'Gesperrt' : 'Erlaubt' }}
                                            </button>
                                        </td>
                                        <td>{{ $seoMeta->updated_at->format('d.m.Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button wire:click="edit({{ $seoMeta->id }})" class="btn btn-outline-primary btn-sm">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button wire:click="confirmDelete({{ $seoMeta->id }})" class="btn btn-outline-danger btn-sm">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                                <button wire:click="deleteAndReset({{ $seoMeta->id }})" class="btn btn-outline-warning btn-sm">
                                                    <i class="ti ti-refresh"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">Keine Einträge gefunden.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div>
                        Zeige {{ $seoMetas->firstItem() }}–{{ $seoMetas->lastItem() }} von {{ $seoMetas->total() }} Einträgen
                    </div>
                    <div>
                        {{ $seoMetas->links('vendor.pagination.app-pagination') }}
                    </div>
                    <div class="d-flex align-items-center">
                        <label for="perPageSelect" class="me-2 mb-0">Einträge pro Seite:</label>
                        <select wire:model.change="perPage" id="perPageSelect" class="form-select form-select-sm">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

