<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Blog Verwaltung</a></li>
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
                                    <th>📝 Titel</th>
                                    <th>🚦 Status</th>
                                    <th>📅 Veröffentlicht am</th>
                                    <th>⚙️ Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($posts as $post)
                                    <tr>
                                        <td>{{ $post->title }}</td>
                                        <td>
                                            @if($post->status === 'published')
                                                <span class="badge bg-success">Veröffentlicht</span>
                                            @elseif($post->status === 'draft')
                                                <span class="badge bg-warning">Entwurf</span>
                                            @elseif($post->status === 'scheduled')
                                                <span class="badge bg-info">Geplant</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($post->publish_start)
                                                {{ \Carbon\Carbon::parse($post->publish_start)->format('d.m.Y') }}
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.blogposts.edit', $post) }}" class="btn btn-sm btn-primary">
                                                Bearbeiten
                                            </a>
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
