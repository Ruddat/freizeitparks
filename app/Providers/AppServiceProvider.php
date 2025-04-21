<?php

namespace App\Providers;

use App\Models\StaticPage;
use App\Services\GeocodeService;
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
    }
}
