<?php

namespace App\Http\Controllers;

use App\Models\Park;
use Illuminate\Http\Request;

class ParkPageController extends Controller
{
    public function summary($parkSlug)
    {
        $park = Park::where('slug', $parkSlug)->firstOrFail();
        return view('parks.summary', compact('park'));
    }

    public function calendar($parkSlug)
    {
        $park = Park::where('slug', $parkSlug)->firstOrFail();
        return view('frontend.pages.park-crowd-calender', compact('park'));
    }

    public function statistics($parkSlug)
    {
        $park = Park::where('slug', $parkSlug)->firstOrFail();
        return view('parks.statistics', compact('park'));
    }
}
