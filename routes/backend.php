<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Backend\Parks\ParkManager;
use App\Livewire\Backend\StaticPages\StaticPageManager;
use App\Livewire\Backend\Marketing\ReferralStatsManager;
use App\Livewire\Backend\Marketing\NewsletterSignupManager;
use App\Livewire\Backend\SettingsComponent\SettingsManager;


Route::prefix('verwaltung')->group(function () {
    Route::view('/', 'backend.dashboard')->name('admin.dashboard');
    Route::get('/parks', ParkManager::class)->name('admin.parks');
    Route::get('/static-pages', StaticPageManager::class)->name('admin.static-pages');
    Route::get('/settings-manager', SettingsManager::class)->name('admin.settings-manager');
    Route::get('/referral-stats', ReferralStatsManager::class)
        ->name('admin.referral-stats');


    Route::get('/newsletter-signups', NewsletterSignupManager::class)
    ->name('admin.newsletter-signups');


});
