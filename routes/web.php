<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParkController;
use App\Http\Controllers\Frontend\IndexController;

//Route::get('/', function () {
//    return view('welcome');
//});
Route::get('/', [IndexController::class, 'index'])->name('home');
Route::get('/scrapper', [IndexController::class, 'testScraper'])->name('testScraper');


Route::get('/parks/{id}', [ParkController::class, 'show'])->name('parks.show');



Route::prefix('verwaltung')->group(function () {
    Route::view('/', 'backend.dashboard')->name('admin.dashboard');
    Route::get('/parks', \App\Livewire\Backend\Parks\ParkManager::class)->name('admin.parks');
});
