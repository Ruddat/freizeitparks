<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ModSiteSettings;

class IndexNowService
{
    protected string $endpoint = 'https://api.indexnow.org/indexnow';

    public function ping(string $url): void
    {
        // Hole Key & Aktivierungsstatus aus der Datenbank
        $enabled = ModSiteSettings::where('key', 'indexnow_enabled')->value('value');
        $key = ModSiteSettings::where('key', 'indexnow_key')->value('value');

        // Nur wenn aktiviert und gültiger Key vorhanden
        if ($enabled != '1' || empty($key) || empty($url)) {
            Log::warning('IndexNow Ping abgebrochen – nicht aktiviert oder fehlender Key/URL.', [
                'enabled' => $enabled,
                'key' => $key,
                'url' => $url
            ]);
            return;
        }

        // Sende Ping
        Http::get($this->endpoint, [
            'url' => $url,
            'key' => $key,
        ]);

        Log::info('✅ IndexNow Ping gesendet', [
            'url' => $url,
            'key' => $key,
        ]);
    }
}
