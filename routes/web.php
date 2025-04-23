<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParkController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\Frontend\IndexController;
use App\Http\Controllers\Frontend\StaticPageController;

//Route::get('/', function () {
//    return view('welcome');
//});
Route::post('/track-dwell-time', [VisitorController::class, 'trackDwellTime'])->name('track.dwell.time');


Route::get('/', [IndexController::class, 'index'])->name('home');
Route::get('/scrapper', [IndexController::class, 'testScraper'])->name('testScraper');


Route::get('/parks/{id}', [ParkController::class, 'show'])->name('parks.show');

Route::get('/seite/{slug}', [StaticPageController::class, 'show'])
    ->name('static.page');




