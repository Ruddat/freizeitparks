<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ModSiteSettings;

class MaintenanceService
{
    private function getSetting(string $key, $default = null)
    {
        $value = ModSiteSettings::where('key', $key)->value('value');
        if (is_null($value)) {
            return $default;
        }
        // Typkonvertierung basierend auf dem Schlüssel
        if ($key === 'maintenance_mode') {
            return (bool) $value; // Cast zu Boolean
        }
        if ($key === 'maintenance_allowed_ips') {
            return json_decode($value, true) ?? $default;
        }
        return $value;
    }

    public function isMaintenanceModeActive(): bool
    {
        $isActive = $this->getSetting('maintenance_mode', false);
        $startAt = $this->getSetting('maintenance_start_at');
        $endAt = $this->getSetting('maintenance_end_at');
        $now = Carbon::now();

        if (!$isActive) {
            return false;
        }

        if ($startAt && $endAt) {
            $start = Carbon::parse($startAt);
            $end = Carbon::parse($endAt);

            // Wenn die Zeit abgelaufen ist, deaktiviere den Wartungsmodus
            if ($now->greaterThan($end)) {
                $this->disableMaintenanceMode();
                return false;
            }

            return $now->between($start, $end);
        }

        return true;
    }

    public function getMaintenanceMessage(): string
    {
        return $this->getSetting('maintenance_message', 'Die Seite befindet sich im Wartungsmodus. Bitte später wiederkommen!');
    }

    public function isIpAllowed(string $ip): bool
    {
        $allowedIps = $this->getSetting('maintenance_allowed_ips', []);
        return in_array($ip, (array) $allowedIps);
    }

    public function getMaintenanceStart(): ?string
    {
        return $this->getSetting('maintenance_start_at');
    }

    public function getMaintenanceEnd(): ?string
    {
        return $this->getSetting('maintenance_end_at');
    }

    public function enableMaintenanceMode($message = null, $startAt = null, $endAt = null, $allowedIps = [])
    {
        DB::table('mod_site_settings')->updateOrInsert(
            ['key' => 'maintenance_mode'],
            ['value' => '1', 'type' => 'boolean', 'group' => 'maintenance', 'updated_at' => now()]
        );
        DB::table('mod_site_settings')->updateOrInsert(
            ['key' => 'maintenance_message'],
            ['value' => $message ?? $this->getMaintenanceMessage(), 'type' => 'string', 'group' => 'maintenance', 'updated_at' => now()]
        );
        DB::table('mod_site_settings')->updateOrInsert(
            ['key' => 'maintenance_start_at'],
            ['value' => $startAt ? Carbon::parse($startAt)->toIso8601String() : null, 'type' => 'string', 'group' => 'maintenance', 'updated_at' => now()]
        );
        DB::table('mod_site_settings')->updateOrInsert(
            ['key' => 'maintenance_end_at'],
            ['value' => $endAt ? Carbon::parse($endAt)->toIso8601String() : null, 'type' => 'string', 'group' => 'maintenance', 'updated_at' => now()]
        );
        DB::table('mod_site_settings')->updateOrInsert(
            ['key' => 'maintenance_allowed_ips'],
            ['value' => json_encode($allowedIps), 'type' => 'json', 'group' => 'maintenance', 'updated_at' => now()]
        );

        Cache::forget('site_setting_maintenance_mode');
        Cache::forget('site_setting_maintenance_message');
        Cache::forget('site_setting_maintenance_start_at');
        Cache::forget('site_setting_maintenance_end_at');
        Cache::forget('site_setting_maintenance_allowed_ips');
    }

    public function disableMaintenanceMode(): void
    {
        DB::table('mod_site_settings')->updateOrInsert(
            ['key' => 'maintenance_mode'],
            ['value' => '0', 'type' => 'boolean', 'group' => 'maintenance', 'updated_at' => now()]
        );
        DB::table('mod_site_settings')->whereIn('key', [
            'maintenance_start_at',
            'maintenance_end_at'
        ])->update(['value' => null, 'updated_at' => now()]);

        Cache::forget('site_setting_maintenance_mode');
        Cache::forget('site_setting_maintenance_start_at');
        Cache::forget('site_setting_maintenance_end_at');
    }
}
