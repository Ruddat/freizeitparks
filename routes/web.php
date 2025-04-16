<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParkController;
use App\Http\Controllers\Frontend\IndexController;

//Route::get('/', function () {
//    return view('welcome');
//});
Route::get('/', [IndexController::class, 'index'])->name('home');

Route::get('/parks/{id}', [ParkController::class, 'show'])->name('parks.show');
