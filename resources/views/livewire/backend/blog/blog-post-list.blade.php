<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <a href="javascript:void(0)">Blog Verwaltung</a>
            </li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">📰 Blogposts verwalten</h4>
                    <div class="input-group search-area w-50">
                        <input type="text" wire:model.debounce.500ms="search" class="form-control"
                               placeholder="🔍 Suche nach Titel...">
                        <span class="input-group-text"><i class="flaticon-381-search-2"></i></span>
                    </div>
                </div>

                <div class="d-flex gap-2 mb-3">
                    <button wire:click="setFilter('all')" class="btn btn-sm {{ $filterStatus === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Alle
                    </button>
                    <button wire:click="setFilter('draft')" class="btn btn-sm {{ $filterStatus === 'draft' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Entwürfe
                    </button>
                    <button wire:click="setFilter('published')" class="btn btn-sm {{ $filterStatus === 'published' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Veröffentlicht
                    </button>
                    <button wire:click="setFilter('scheduled')" class="btn btn-sm {{ $filterStatus === 'scheduled' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Geplant
                    </button>

                    <!-- NEU: Coupon-Filter -->
                    <button wire:click="toggleCouponFilter" class="btn btn-sm {{ $filterCouponOnly ? 'btn-success' : 'btn-outline-success' }}">
                        🎟️ Nur mit Coupon
                    </button>

                    <!-- NEU: Reset-Button -->
                    <button wire:click="resetFilters" class="btn btn-sm btn-outline-secondary">
                        🔄 Filter zurücksetzen
                    </button>
                </div>



                <div class="card-body">
                    <div class="mb-3">
                        <a href="{{ route('admin.blogposts.create') }}" class="btn btn-primary">
                            + Neuer Blogpost
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th style="cursor: pointer;" wire:click="sortBy('title')">
                                        📝 Titel
                                        @if ($sortField === 'title')
                                            @if ($sortDirection === 'asc') ↑ @else ↓ @endif
                                        @endif
                                    </th>
                                    <th style="cursor: pointer;" wire:click="sortBy('status')">
                                        🚦 Status
                                        @if ($sortField === 'status')
                                            @if ($sortDirection === 'asc') ↑ @else ↓ @endif
                                        @endif
                                    </th>
                                    <th style="cursor: pointer;" wire:click="sortBy('publish_start')">
                                        📅 Veröffentlicht
                                        @if ($sortField === 'publish_start')
                                            @if ($sortDirection === 'asc') ↑ @else ↓ @endif
                                        @endif
                                    </th>
                                    <th>⚙️ Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($posts as $post)
                                <tr>
                                    <!-- Titel -->
                                    <td>
                                        @if ($editingPostId === $post->id)
                                            <input type="text" wire:model.defer="editingTitle" class="form-control form-control-sm">
                                        @else
                                            <span wire:click="startEditing({{ $post->id }})" style="cursor: pointer;" class="text-primary">
                                                {{ $post->title }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Status -->
                                    <td>
                                        @if ($editingPostId === $post->id)
                                            <select wire:model.defer="editingStatus" class="form-select form-select-sm">
                                                <option value="draft">Entwurf</option>
                                                <option value="published">Veröffentlicht</option>
                                                <option value="scheduled">Geplant</option>
                                            </select>
                                        @else
                                            @if ($post->status === 'published')
                                                <span class="badge bg-success">✅ Online</span>
                                            @elseif ($post->status === 'draft')
                                                <span class="badge bg-warning">🚧 Entwurf</span>
                                            @elseif ($post->status === 'scheduled')
                                                <span class="badge bg-info">🕒 Geplant</span>
                                            @endif
                                        @endif
                                    </td>

                                    <!-- Veröffentlichungsdatum -->
                                    <td>
                                        {{ $post->publish_start ? \Carbon\Carbon::parse($post->publish_start)->format('d.m.Y') : '-' }}
                                    </td>

                                    <!-- Aktionen -->
                                    <td class="space-x-2">
                                        @if ($editingPostId === $post->id)
                                            <button wire:click="saveEditing" class="btn btn-success btn-sm">💾 Speichern</button>
                                            <button wire:click="$set('editingPostId', null)" class="btn btn-secondary btn-sm">❌ Abbrechen</button>
                                        @else
                                            <a href="{{ route('admin.blogposts.edit', $post) }}" class="btn btn-sm btn-primary">
                                                Bearbeiten
                                            </a>
                                        @endif
                                    </td>
                                </tr>

                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Keine Blogposts gefunden.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Zeige {{ $posts->firstItem() }}–{{ $posts->lastItem() }} von {{ $posts->total() }} Einträgen
                        </div>
                        {{ $posts->links('vendor.pagination.app-pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
