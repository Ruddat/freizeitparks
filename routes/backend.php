<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Livewire\Backend\Parks\ParkManager;
use App\Livewire\Backend\Marketing\SeoMetaEditManager;
use App\Livewire\Backend\StaticPages\StaticPageManager;
use App\Livewire\Backend\Marketing\ReferralStatsManager;
use App\Livewire\Backend\Marketing\NewsletterSignupManager;
use App\Livewire\Backend\SettingsComponent\SettingsManager;




Route::get('/admin/backup/download', function (\Illuminate\Http\Request $request) {
    $encodedPath = $request->query('path');

    if (!$encodedPath) {
        abort(404, 'Kein Pfad angegeben.');
    }

    $relativePath = base64_decode($encodedPath);

    $fullPath = storage_path('app/' . $relativePath);

    if (!file_exists($fullPath)) {
        abort(404, 'Backup nicht gefunden.');
    }

    return response()->download($fullPath);
})->name('admin.download-backup');



Route::prefix('verwaltung')->group(function () {
    Route::view('/', 'backend.dashboard')->name('admin.dashboard');
    Route::get('/parks', ParkManager::class)->name('admin.parks');
    Route::get('/static-pages', StaticPageManager::class)->name('admin.static-pages');
    Route::get('/settings-manager', SettingsManager::class)->name('admin.settings-manager');
    Route::get('/seo-manager', \App\Livewire\Backend\Marketing\SeoTableManager::class)
        ->name('admin.seo-manager');
    Route::get('/seo/edit/{id}', SeoMetaEditManager::class)->name('seo.edit');

    Route::get('/referral-stats', ReferralStatsManager::class)
        ->name('admin.referral-stats');


    Route::get('/newsletter-signups', NewsletterSignupManager::class)
    ->name('admin.newsletter-signups');

    Route::get('/backup-manager', \App\Livewire\Backend\Backup\BackupManager::class)
        ->name('admin.backup-manager');


});
