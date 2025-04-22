<?php

use App\Http\Middleware\TrackReferral;
use Illuminate\Foundation\Application;
use App\Http\Middleware\TrackVisitorSession;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Admin-Routen registrieren
            Route::middleware('web')
                ->group(base_path('routes/backend.php'));
            // API-Routen registrieren
            // Standard Web-Routen registrieren
            Route::middleware('web', 'track-referral', 'track-visitor')
            ->group(base_path('routes/web.php'));
            },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
           // 'maintenance' => CheckMaintenanceMode::class, // Alias korrekt definiert
            'track-referral' => TrackReferral::class,
            'track-visitor' => TrackVisitorSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
