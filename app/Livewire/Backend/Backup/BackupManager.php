<?php

namespace App\Livewire\Backend\Backup;

use Exception;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupManager extends Component
{
    public $isRunning = false;
    public $message = null;
    public $backups = [];

    public function mount()
    {
        $this->loadBackups();
    }

    public function createBackup()
    {
        $this->isRunning = true;
        $this->message = null;

        try {
            Artisan::call('backup:run --only-db'); // Backup anstoßen
            $this->message = 'Backup erfolgreich erstellt!';
        } catch (\Exception $e) {
            $this->message = 'Fehler beim Erstellen des Backups: ' . $e->getMessage();
        }

        $this->isRunning = false;

        $this->loadBackups(); // Backups neu laden!
    }

    public function loadBackups()
    {
        $this->backups = [];

        $backupFolder = storage_path('app/private/Laravel'); // Absoluter Pfad zum Backup-Ordner

        if (!is_dir($backupFolder)) {
            return; // Falls Ordner nicht existiert, nichts tun
        }

        $files = glob($backupFolder . '/*.zip'); // Nur ZIP-Dateien holen

        foreach ($files as $file) {
            $this->backups[] = [
                'path' => str_replace(storage_path('app/'), '', $file), // Pfad relativ zur Storage-Disk
                'name' => basename($file),
                'size' => filesize($file),
                'lastModified' => filemtime($file),
            ];
        }

        // Neueste zuerst sortieren
        usort($this->backups, function($a, $b) {
            return $b['lastModified'] <=> $a['lastModified'];
        });
    }


    public function download($path)
    {
        return response()->download(storage_path('app/' . $path));
    }

    public function delete($path)
    {
        try {
            Storage::disk('local')->delete($path);
            $this->message = 'Backup erfolgreich gelöscht!';
        } catch (\Exception $e) {
            $this->message = 'Fehler beim Löschen: ' . $e->getMessage();
        }

        $this->loadBackups();
    }

    public function getEncodedPath($path)
    {
        return base64_encode($path);
    }


    public function render()
    {
        return view('livewire.backend.backup.backup-manager')
            ->layout('backend.layouts.backend');
    }
}
