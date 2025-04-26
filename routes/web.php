<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParkController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\ParkPageController;
use App\Http\Controllers\Frontend\IndexController;
use App\Http\Controllers\Frontend\Seo\RobotsController;
use App\Http\Controllers\Frontend\StaticPageController;
use App\Http\Controllers\Frontend\Seo\SitemapController;

//Route::get('/', function () {
//    return view('welcome');
//});
Route::post('/track-dwell-time', [VisitorController::class, 'trackDwellTime'])->name('track.dwell.time');


Route::get('/', [IndexController::class, 'index'])->name('home');
Route::get('/scrapper', [IndexController::class, 'testScraper'])->name('testScraper');


// Route::get('/parks/{id}', [ParkController::class, 'show'])->name('parks.show');
Route::get('/parks/{identifier}', [ParkController::class, 'show'])->name('parks.show');

Route::get('/seite/{slug}', [StaticPageController::class, 'show'])
    ->name('static.page');

    Route::get('/robots.txt', [\App\Http\Controllers\Frontend\Seo\RobotsController::class, 'index']);
    Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.xml');


    Route::prefix('parks')->group(function () {
        Route::get('/{parkSlug}/summary', [ParkPageController::class, 'summary'])->name('parks.summary');
        Route::get('/{parkSlug}/calendar', [ParkPageController::class, 'calendar'])->name('parks.calendar');
        Route::get('/{parkSlug}/statistics', [ParkPageController::class, 'statistics'])->name('parks.statistics');
    });
