<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Parks verwalten</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">🎢 Parks Übersicht</h4>
                    <div class="d-flex align-items-center">
                        <div class="input-group search-area me-3">
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="🔍 Suche nach Park...">
                            <span class="input-group-text"><i class="flaticon-381-search-2"></i></span>
                        </div>
                        <button class="btn btn-success ms-2" wire:click="create">
                            <i class="flaticon-381-add"></i> Neuer Park
                        </button>
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
                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if (session()->has('warning'))
                        <div class="alert alert-warning alert-dismissible fade show">
                            {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Land</th>
                                    <th>Status</th>
                                    <th>Kontinent</th>
                                    <th>Kategorie</th>
                                    <th class="text-center">Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($parks as $park)
                                    <tr>
                                        <td>{{ $park->name }}</td>
                                        <td>{{ $park->country }}</td>
                                        <td>
                                            <a href="javascript:void(0)" wire:click="toggleStatus({{ $park->id }})" class="badge text-white
                                                @if($park->status === 'active') bg-success
                                                @elseif($park->status === 'pending') bg-warning
                                                @elseif($park->status === 'revive') bg-info
                                                @elseif($park->status === 'inactive') bg-secondary
                                                @else bg-dark
                                                @endif
                                                ">
                                                {{ ucfirst($park->status) }}
                                            </a>
                                        </td>
                                        <td>{{ $park->continent }}</td>
                                        <td>{{ $park->type }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <a href="javascript:void(0)"
                                                   wire:click="edit({{ $park->id }})"
                                                   class="btn btn-warning btn-sm mx-1">
                                                    <i class="flaticon-381-edit"></i>
                                                </a>
                                                <a href="javascript:void(0)"
                                                   wire:click="delete({{ $park->id }})"
                                                   class="btn btn-danger btn-sm mx-1"
                                                   onclick="return confirm('Wirklich löschen?')">
                                                    <i class="flaticon-381-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Zeige {{ $parks->firstItem() }}-{{ $parks->lastItem() }} von {{ $parks->total() }} Einträgen
                        </div>
                        {{ $parks->links('vendor.pagination.app-pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Park Modal -->
    <div class="modal fade" id="editParkModal" tabindex="-1" aria-labelledby="editParkModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editParkModalLabel">🛠️ Park bearbeiten</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="closeModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Read-only fields -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ID</label>
                            <input type="text" class="form-control" value="{{ $editingPark['id'] }}" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Erstellt am</label>
                            <input type="text" class="form-control" value="{{ $editingPark['created_at'] }}" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Aktualisiert am</label>
                            <input type="text" class="form-control input-disabled" value="{{ $editingPark['updated_at'] }}" disabled>
                        </div>

                        <!-- Editable fields -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Externe ID</label>
                            <input type="text" wire:model="editingPark.external_id" class="form-control" disabled>
                            @error('editingPark.external_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Queue Times ID</label>
                            <input type="number" wire:model="editingPark.queue_times_id" class="form-control" disabled>
                            @error('editingPark.queue_times_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gruppen-ID</label>
                            <input type="number" wire:model="editingPark.group_id" class="form-control" disabled>
                            @error('editingPark.group_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" wire:model="editingPark.name" class="form-control">
                            @error('editingPark.name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gruppenname</label>
                            <input type="text" wire:model="editingPark.group_name" class="form-control">
                            @error('editingPark.group_name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Standort</label>
                            <input type="text" wire:model="editingPark.location" class="form-control">
                            @error('editingPark.location') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Straße</label>
                            <input type="text" wire:model="editingPark.street" class="form-control">
                            @error('editingPark.street') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">PLZ</label>
                            <input type="text" wire:model="editingPark.zip" class="form-control">
                            @error('editingPark.zip') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Stadt</label>
                            <input type="text" wire:model="editingPark.city" class="form-control">
                            @error('editingPark.city') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Land</label>
                            <input type="text" wire:model="editingPark.country" class="form-control">
                            @error('editingPark.country') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kontinent</label>
                            <input type="text" wire:model="editingPark.continent" class="form-control">
                            @error('editing103Park.continent') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Zeitzone</label>
                            <input type="text" wire:model="editingPark.timezone" class="form-control">
                            @error('editingPark.timezone') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status *</label>
                            <select wire:model="editingPark.status" class="form-select">
                                <option value="">-- Status auswählen --</option>
                                <option value="pending">⏳ Wartend</option>
                                <option value="active">✅ Aktiv</option>
                                <option value="inactive">⛔ Inaktiv</option>
                                <option value="revive">♻️ Wiederbeleben</option>
                            </select>
                            @error('editingPark.status') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Park-Bild hochladen</label>
                            <input type="file" wire:model="imageUpload" class="form-control" accept="image/*">
                            @error('imageUpload') <span class="text-danger">{{ $message }}</span> @enderror
                            @if ($imageUpload)
                                <div class="mt-2">
                                    <img src="{{ $imageUpload->temporaryUrl() }}" alt="Bild Vorschau" style="max-width: 100px;">
                                </div>
                                @elseif ($editingPark['image'] && file_exists(public_path($editingPark['image'])))
                                <div class="mt-2">
                                    <img src="{{ asset($editingPark['image']) }}" alt="Aktuelles Bild" style="max-width: 100px;">
                                </div>
                            @else
                                <div class="mt-2">
                                    <img src="{{ asset('images/no-image.png') }}" alt="Kein Bild vorhanden" style="max-width: 100px;">
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Breitengrad</label>
                            <input type="number" step="0.0000001" wire:model="editingPark.latitude" class="form-control">
                            @error('editingPark.latitude') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Längengrad</label>
                            <input type="number" step="0.0000001" wire:model="editingPark.longitude" class="form-control">
                            @error('editingPark.longitude') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <button type="button" wire:click="fetchGeodata" class="btn btn-outline-primary w-100">
                                📍 Geodaten automatisch abrufen (OpenStreetMap)
                            </button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Website-URL</label>
                            <input type="url" wire:model="editingPark.url" class="form-control">
                            @error('editingPark.url') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Video-Embed-Code</label>
                            <textarea wire:model="editingPark.video_embed_code" class="form-control" rows="3"></textarea>
                            @error('editingPark.video_embed_code') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Video-URL</label>
                            <input type="url" wire:model="editingPark.video_url" class="form-control">
                            @error('editingPark.video_url') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Logo hochladen</label>
                            <input type="file" wire:model="logoUpload" class="form-control" accept="image/*">
                            @error('logoUpload') <span class="text-danger">{{ $message }}</span> @enderror
                            @if ($logoUpload)
                            <div class="mt-2">
                                <img src="{{ $logoUpload->temporaryUrl() }}" alt="Logo Vorschau" style="max-width: 100px;">
                            </div>
                        @elseif (!empty($editingPark['logo']))
                            <div class="mt-2">
                                <img src="{{ Str::startsWith($editingPark['logo'], 'storage/') ? asset($editingPark['logo']) : Storage::url($editingPark['logo']) }}"
                                     alt="Aktuelles Logo"
                                     style="max-width: 100px;">
                            </div>
                        @endif
                        </div>
<!-- Beschreibung mit Jodit -->
<div class="col-md-12 mb-3">
    <label class="form-label">Beschreibung</label>
    <div class="row mb-2">
        <div class="col-md-8">
            <input type="text" wire:model="keywords" class="form-control" placeholder="Stichworte für Generierung (z. B. Achterbahnen, Familienfreundlich, Abenteuer)">
            @error('keywords') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="col-md-4">
            <button type="button" wire:click="generateDescription" class="btn btn-info w-100" wire:loading.attr="disabled">
                <span wire:loading>Beschreibung wird generiert...</span>
                <span wire:loading.remove>Beschreibung generieren</span>
            </button>
        </div>
    </div>
    <textarea wire:model="editingPark.description" id="descriptionEditor" class="form-control"></textarea>
    @error('editingPark.description') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<!-- Öffnungszeiten-Bereich im Modal -->
<div class="col-md-12 mb-4">
    <label class="form-label">Öffnungszeiten</label>

    @if ($editingPark['id'])
        <livewire:backend.parks.opening-times-editor :park-id="$editingPark['id']" wire:key="opening-times-{{ $editingPark['id'] }}" />
    @else
        <div class="alert alert-warning">Bitte speichere den Park zuerst, bevor Öffnungszeiten bearbeitet werden können.</div>
    @endif
</div>



                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategorie</label>
                            <select wire:model="editingPark.type" class="form-select">
                                <option value="">-- Kategorie auswählen --</option>
                                @foreach ($parkTypes as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                            @error('editingPark.type') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="closeModal">Abbrechen</button>
                    <button type="button" class="btn btn-primary" wire:click="update" data-bs-dismiss="modal">Speichern</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .input-disabled {
    background-color: #f8f9fa !important;
    color: #6c757d !important;
    cursor: not-allowed;
}
</style>
@endpush


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jodit@4.0.0-beta.178/build/jodit.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/jodit@4.0.0-beta.178/build/jodit.min.css" rel="stylesheet">
<script>
document.addEventListener('livewire:navigated', () => {
    const modalElement = document.getElementById('editParkModal');
    if (modalElement) {
        new bootstrap.Modal(modalElement);
    }
});

Livewire.on('open-modal', () => {
    const modalElement = document.getElementById('editParkModal');
    if (modalElement) {
        const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
        modal.show();
        if (document.getElementById('descriptionEditor') && !window.Jodit.instances.descriptionEditor) {
            window.joditEditor = new Jodit('#descriptionEditor', {
                height: 400,
                buttons: [
                    'bold', 'italic', 'underline', '|',
                    'ul', 'ol', '|',
                    'outdent', 'indent', '|',
                    'font', 'fontsize', 'brush', 'paragraph', '|',
                    'image', 'link', '|',
                    'align', 'undo', 'redo', '|',
                    'cut', 'copy', 'paste', '|',
                    'source'
                ],
                placeholder: 'Beschreibung eingeben oder generieren...',
                uploader: {
                    insertImageAsBase64URI: true
                }
            });
        }
    }
});

Livewire.on('updateJoditEditor', (content) => {
    if (window.joditEditor) {
        window.joditEditor.setEditorValue(content);
        console.log('Jodit Editor aktualisiert:', content.substring(0, 100)); // Debugging
    } else {
        console.error('Jodit Editor nicht initialisiert');
    }
});
</script>
@endpush
