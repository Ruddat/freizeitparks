<?php

namespace App\Http\Controllers\Frontend;

use App\Models\StaticPage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class StaticPageController extends Controller
{
    public function show(string $slug): View
    {
        $page = StaticPage::where('slug', $slug)->firstOrFail();

        return view('frontend.pages.static-page', compact('page'));
    }
}
