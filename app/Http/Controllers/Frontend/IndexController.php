<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Park;
use Illuminate\Http\Request;
use App\Services\WeatherService;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    protected WeatherService $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function index()
    {
       // $parks = Park::with('country')->limit(8)->get();
        $forecast = app(\App\Services\WeatherService::class)->getSevenDayForecast();

       // dd($forecast);
        //return view('welcome', compact('parks'));
        return view('welcome', compact('forecast'));

    }
}
