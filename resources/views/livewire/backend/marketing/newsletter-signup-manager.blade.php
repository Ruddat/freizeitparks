<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Newsletter</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">📬 Newsletter-Anmeldungen</h4>
                    <div class="input-group search-area w-50">
                        <input type="text" wire:model.debounce.500ms="search" class="form-control"
                               placeholder="🔍 Suche nach E-Mail...">
                        <span class="input-group-text"><i class="flaticon-381-search-2"></i></span>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>📧 E-Mail</th>
                                    <th>🧑 Name</th>
                                    <th>🏡 Ort</th>
                                    <th>🎯 Interessen</th>
                                    <th>✅ Bestätigt</th>
                                    <th>📅 Angemeldet am</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($signups as $signup)
                                    <tr>
                                        <td>{{ $signup->email }}</td>
                                        <td>{{ $signup->name }}</td>
                                        <td>{{ $signup->city }}</td>
                                        <td>
                                            @if($signup->interests)
                                                <ul class="mb-0 ps-3">
                                                    @foreach($signup->interests as $interest)
                                                        <li>{{ ucfirst($interest) }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </td>
                                        <td>
                                            @if($signup->confirmed_at)
                                                <span class="badge bg-success">Ja</span>
                                            @else
                                                <span class="badge bg-danger">Nein</span>
                                            @endif
                                        </td>
                                        <td>{{ $signup->created_at->format('d.m.Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Keine Einträge gefunden.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Zeige {{ $signups->firstItem() }}–{{ $signups->lastItem() }} von {{ $signups->total() }} Einträgen
                        </div>
                        {{ $signups->links('vendor.pagination.app-pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
