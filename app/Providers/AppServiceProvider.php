<?php

namespace App\Providers;

use App\Models\StaticPage;
use BladeUI\Icons\Factory;
use App\Models\ModSiteSettings;
use App\Services\GeocodeService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GeocodeService::class, function ($app) {
            return new GeocodeService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            $view->with('navPages', StaticPage::where('show_in_nav', true)->get());
            $view->with('footerPages', StaticPage::where('show_in_footer', true)->get());
        });

        View::composer('*', function ($view) {
            $view->with('siteSettings', ModSiteSettings::getPublicSettings());
        });

        // ✅ Nur registrieren, wenn Ordner wirklich existiert
        $lucidePath = base_path('node_modules/lucide-static/icons');

        if (File::isDirectory($lucidePath)) {
            $this->app->make(Factory::class)->add('lucide', [
                'path' => $lucidePath,
                'prefix' => 'lucide',
            ]);
        }
    }
}
