<?php

namespace App\Http\Controllers\Frontend\Seo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class RobotsController extends Controller
{
    public function index(Request $request): Response
    {
        $lines = [];

        $host = $request->getSchemeAndHttpHost(); // gibt z. B. https://freizeitparks.de
        $sitemapUrl = $host . '/sitemap.xml';

        if (App::environment('production')) {
            $lines[] = 'User-agent: *';
            $lines[] = 'Disallow: /admin';
            $lines[] = 'Disallow: /login';
            $lines[] = 'Allow: /';
            $lines[] = 'Sitemap: ' . $sitemapUrl;
        } else {
            $lines[] = 'User-agent: *';
            $lines[] = 'Disallow: /';
            $lines[] = '# Diese Seite ist nicht für Indexierung freigegeben.';
            $lines[] = '# Umgebung: ' . App::environment();
        }

        return response(implode(PHP_EOL, $lines), 200)
            ->header('Content-Type', 'text/plain');
    }
}
