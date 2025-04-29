<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <a href="{{ route('admin.categories') }}">Kategorien</a>
            </li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">📚 Kategorien verwalten</h4>

                    <form wire:submit.prevent="save" class="d-flex gap-2">
                        <input type="text" wire:model.defer="name" class="form-control" placeholder="Neue Kategorie">
                        <button type="submit" class="btn btn-primary">+ Speichern</button>
                    </form>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>📁 Kategorie-Name</th>
                                    <th>⚙️ Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>{{ $category->name }}</td>
                                        <td>
                                            <button wire:click="delete({{ $category->id }})" class="btn btn-sm btn-danger">
                                                ❌ Löschen
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">
                                            Keine Kategorien gefunden.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
