<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\View\View;
use App\Models\StaticPage;
use App\Services\SeoService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StaticPageController extends Controller
{
    public function show(string $slug, SeoService $seoService): View
    {
        $page = StaticPage::where('slug', $slug)->firstOrFail();

        // SEO generieren mit Speicherung in mod_seo_metas
        $seo = $seoService->getSeoData($page);

        return view('frontend.pages.static-page', compact('page', 'seo'));
    }
}
