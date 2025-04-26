<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Backups verwalten</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">🛡️ Backup Manager</h4>

                    <button wire:click="createBackup"
                        class="btn btn-primary {{ $isRunning ? 'disabled' : '' }}"
                        @disabled($isRunning)>
                        @if ($isRunning)
                            <i class="flaticon-381-loading spin"></i> Backup läuft...
                        @else
                            <i class="flaticon-381-disk"></i> Backup erstellen
                        @endif
                    </button>
                </div>

                <div class="card-body">

                    @if ($message)
                        <div class="alert {{ str_contains($message, 'Fehler') ? 'alert-danger' : 'alert-success' }}">
                            {{ $message }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Dateiname</th>
                                    <th>Größe</th>
                                    <th>Erstellt am</th>
                                    <th class="text-center">Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($backups as $backup)
                                    <tr>
                                        <td>{{ $backup['name'] }}</td>
                                        <td>{{ number_format($backup['size'] / 1024 / 1024, 2) }} MB</td>
                                        <td>{{ \Carbon\Carbon::createFromTimestamp($backup['lastModified'])->format('d.m.Y H:i') }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <a href="{{ route('admin.download-backup', ['path' => base64_encode($backup['path'])]) }}"
                                                    target="_blank"
                                                    class="btn btn-success btn-sm mx-1">
                                                    <i class="flaticon-381-download"></i> Download
                                                </a>

                                                <button wire:click="delete('{{ $backup['path'] }}')"
                                                    class="btn btn-danger btn-sm mx-1"
                                                    onclick="return confirm('Backup wirklich löschen?')">
                                                    <i class="flaticon-381-trash"></i> Löschen
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                                @if(empty($backups))
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Keine Backups vorhanden.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
