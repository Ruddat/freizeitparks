<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParkController;
use App\Http\Controllers\Frontend\IndexController;
use App\Http\Controllers\Frontend\StaticPageController;
use App\Livewire\Backend\SettingsComponent\SettingsManager;

//Route::get('/', function () {
//    return view('welcome');
//});
Route::get('/', [IndexController::class, 'index'])->name('home');
Route::get('/scrapper', [IndexController::class, 'testScraper'])->name('testScraper');


Route::get('/parks/{id}', [ParkController::class, 'show'])->name('parks.show');

Route::get('/seite/{slug}', [StaticPageController::class, 'show'])
    ->name('static.page');



Route::prefix('verwaltung')->group(function () {
    Route::view('/', 'backend.dashboard')->name('admin.dashboard');
    Route::get('/parks', \App\Livewire\Backend\Parks\ParkManager::class)->name('admin.parks');
    Route::get('/static-pages', \App\Livewire\Backend\StaticPages\StaticPageManager::class)->name('admin.static-pages');
    Route::get('/settings-manager', SettingsManager::class)->name('admin.settings-manager');
    Route::get('/newsletter-signups', \App\Livewire\Backend\Marketing\NewsletterSignupManager::class)
    ->name('admin.newsletter-signups');
});
