<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParkController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\ParkPageController;
use App\Http\Controllers\Frontend\IndexController;
use App\Http\Controllers\Frontend\Blog\BlogController;
use App\Http\Controllers\Frontend\Seo\RobotsController;
use App\Http\Controllers\Frontend\StaticPageController;
use App\Http\Controllers\Frontend\Seo\SitemapController;
use App\Http\Controllers\Frontend\Park\ParkTopicController;
use App\Http\Controllers\Frontend\Widgets\WidgetController;

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


    Route::get('/themen/{slug}-tipps', [ParkTopicController::class, 'show'])->name('themen.park');
    Route::get('/themen/{slug}', [ParkTopicController::class, 'show'])->name('themen.park');

    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
    Route::get('/blog/kategorie/{slug}', [BlogController::class, 'category'])->name('blog.category');
    Route::get('/blog/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');

    Route::get('/widgets/{park}/trend', [WidgetController::class, 'trend'])
    ->name('widgets.trend');

    Route::get('/widgets', [WidgetController::class, 'overview'])
    ->name('widgets.overview');

    Route::prefix('parks')->name('parks.')->group(function () {
        Route::get('/{park}/summary', [ParkController::class, 'summary'])->name('summary');
        Route::get('/{park}/calendar', [ParkController::class, 'calendar'])->name('calendar');
        Route::get('/{park}/statistics', [ParkController::class, 'statistics'])->name('statistics');
        Route::get('/{park}/summary', [ParkController::class, 'summary'])->name('summary');
        Route::get('/{park}', [ParkController::class, 'show'])->name('show'); // letzter!
    });





