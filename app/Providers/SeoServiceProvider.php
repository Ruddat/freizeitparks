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
            'title' => 'ReiseAffe24.de - Deine Reiseplattform',
            'description' => 'Finde die besten Reiseziele, Wetterdaten und Top-Locations für deinen nächsten Urlaub.',
            'canonical' => url('/'),
            'extra_meta' => [],
        ]);
    }
}
