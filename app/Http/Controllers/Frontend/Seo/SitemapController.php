<?php

namespace App\Http\Controllers\Frontend\Seo;

use App\Models\Park;
use App\Models\BlogPost;
use App\Models\StaticPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function index(Request $request)
    {
        $urls = [];

        // Startseite
        $urls[] = [
            'loc' => url('/'),
            'priority' => '1.0',
            'changefreq' => 'daily',
        ];

        // Statische Seiten
        foreach (StaticPage::all() as $page) {
            $urls[] = [
                'loc' => route('static.page', $page->slug),
                'priority' => '0.6',
                'changefreq' => 'monthly',
                'lastmod' => optional($page->updated_at)->toW3cString(),
            ];
        }

        // Parks
        foreach (Park::where('status', 'active')->get() as $park) {
            $urls[] = [
                'loc' => route('parks.show', $park->slug),
                'priority' => '0.8',
                'changefreq' => 'weekly',
                'lastmod' => optional($park->updated_at)->toW3cString(),
            ];
        }

        // Blog-Übersichtsseite
        $urls[] = [
            'loc' => route('blog.index'),
            'priority' => '0.7',
            'changefreq' => 'weekly',
        ];

        // Einzelne Blog-Beiträge
        foreach (\App\Models\BlogPost::where('status', 'published')->get() as $post) {
            $urls[] = [
                'loc' => route('blog.show', $post->slug),
                'priority' => '0.5',
                'changefreq' => 'monthly',
                'lastmod' => optional($post->updated_at)->toW3cString(),
            ];
        }

        $xml = view('sitemap.xml', ['urls' => $urls]);

        return Response::make($xml, 200)->header('Content-Type', 'application/xml');
    }
}
