<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SeoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::share('seo', [
            'title' => 'Freizeitparks entdecken & vergleichen – Parkverzeichnis.de',
            'description' => 'Finde Freizeitparks weltweit – mit Bewertungen, Öffnungszeiten, Besuchermeinungen & Parktipps. Jetzt entdecken auf Parkverzeichnis.de!',
            'canonical' => url('/'),
            'extra_meta' => [],
        ]);
    }
}
