<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\MaintenanceService;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    protected $maintenanceService;

    public function __construct(MaintenanceService $maintenanceService)
    {
        $this->maintenanceService = $maintenanceService;
    }

    public function handle(Request $request, Closure $next)
    {
        // Prüfe, ob der Wartungsmodus aktiv ist (inklusive Zeitfenster)
        if ($this->maintenanceService->isMaintenanceModeActive()) {
            $userIp = $request->ip();

            // Erlaube Zugriff für erlaubte IPs oder authentifizierte Admins
            if ($this->maintenanceService->isIpAllowed($userIp) || ($request->user() && $request->user()->isAdmin())) {
                return $next($request);
            }

            // Zeige die Wartungsseite mit der Nachricht an
            return response()->view('maintenance', [
                'message' => $this->maintenanceService->getMaintenanceMessage(),
                'start_at' => $this->maintenanceService->getMaintenanceStart(), // Optional
                'end_at' => $this->maintenanceService->getMaintenanceEnd(),     // Optional
            ], 503);
        }

        return $next($request);
    }
}
