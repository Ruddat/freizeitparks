<?php

namespace App\Http\Controllers\Frontend\Park;

use App\Models\Park;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SeoTextGeneratorService;

class ParkTopicController extends Controller
{
    public function show(string $slug)
    {
        $cleanSlug = str_replace('-tipps', '', $slug);

        $park = Park::with(['queueTimes', 'openingHours'])->where('slug', $cleanSlug)->firstOrFail();

        if (empty($park->seo_text)) {
            $seoText = app(SeoTextGeneratorService::class)->generateSeoTextFor($park);

            if ($seoText) {
                $park->update(['seo_text' => $seoText]);
            }
        }

        $topAttractions = $park->queueTimes
            ->sortByDesc('wait_time')
            ->take(3);

        return view('frontend.pages.park.themes.park-topic', compact('park', 'topAttractions'));
    }
}
