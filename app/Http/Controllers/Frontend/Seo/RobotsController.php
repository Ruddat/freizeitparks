<?php

namespace App\Http\Controllers\Frontend\Seo;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class RobotsController extends Controller
{
    public function index(Request $request): Response
    {
        $lines = [];

        $host = $request->getSchemeAndHttpHost(); // z. B. https://parkverzeichnis.de
        $sitemapUrl = $host . '/sitemap.xml';

        if (app()->environment('production')) {
            // Korrekte Reihenfolge: Allow zuerst
            $lines[] = 'User-agent: *';
            $lines[] = 'Allow: /';
            $lines[] = 'Disallow: /verwaltung';
            $lines[] = 'Disallow: /admin';
            $lines[] = 'Disallow: /login';
            $lines[] = 'Sitemap: ' . $sitemapUrl;
        } else {
            // Entwicklungsumgebungen komplett blockieren
            $lines[] = 'User-agent: *';
            $lines[] = 'Disallow: /';
            $lines[] = '# Nicht freigegeben für Indexierung – Umgebung: ' . app()->environment();
        }

        return new Response(implode(PHP_EOL, $lines), 200, [
            'Content-Type' => 'text/plain',
        ]);
    }
}
