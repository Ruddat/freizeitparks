<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Statische Seiten verwalten</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">📄 Statische Seiten</h4>
                    @if($editId)
                        <button class="btn btn-secondary" wire:click="resetForm">
                            <i class="flaticon-381-close"></i> Abbrechen
                        </button>
                    @endif
                </div>

                <div class="card-body">
                    <form wire:submit.prevent="save" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Titel</label>
                            <input type="text" wire:model.defer="title" class="form-control" placeholder="z. B. Impressum">
                            @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Slug</label>
                            <input type="text" wire:model.defer="slug" class="form-control" placeholder="z. B. impressum">
                            @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Inhalt</label>

                            <div wire:ignore>
                                <textarea id="tinymce-editor" wire:model.defer="content_txt">{!! $content_txt !!}</textarea>
                            </div>
                            @error('content_txt') <small class="text-danger">{{ $message }}</small> @enderror

                        <div class="alert alert-info">Livewire-Wert: <code>{{ Str::limit($content_txt, 100) }}</code></div>

                        </div>


                        </div>
                        <div class="col-md-3 form-check">
                            <input type="checkbox" class="form-check-input" id="footerCheck" wire:model.defer="show_in_footer">
                            <label class="form-check-label" for="footerCheck">Im Footer anzeigen</label>
                        </div>
                        <div class="col-md-3 form-check">
                            <input type="checkbox" class="form-check-input" id="navCheck" wire:model.defer="show_in_nav">
                            <label class="form-check-label" for="navCheck">In Navigation anzeigen</label>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="flaticon-381-disk"></i> {{ $editId ? 'Aktualisieren' : 'Erstellen' }}
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Titel</th>
                                    <th>Slug</th>
                                    <th class="text-center">Footer</th>
                                    <th class="text-center">Navigation</th>
                                    <th class="text-center">Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pages as $page)
                                    <tr>
                                        <td>{{ $page->title }}</td>
                                        <td><code>{{ $page->slug }}</code></td>
                                        <td class="text-center">{!! $page->show_in_footer ? '✅' : '❌' !!}</td>
                                        <td class="text-center">{!! $page->show_in_nav ? '✅' : '❌' !!}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <button wire:click="edit({{ $page->id }})" class="btn btn-warning btn-sm mx-1">
                                                    <i class="flaticon-381-edit"></i>
                                                </button>
                                                <button wire:click="delete({{ $page->id }})" class="btn btn-danger btn-sm mx-1" onclick="return confirm('Wirklich löschen?')">
                                                    <i class="flaticon-381-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($pages->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Keine statischen Seiten vorhanden.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>

