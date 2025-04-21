<?php

namespace App\Livewire\Frontend;

use Exception;
use Livewire\Component;
use App\Models\Park;
use App\Models\ParkCrowdReport;
use GeoIp2\Database\Reader;

class ParkCrowdIntroComponent extends Component
{
    public Park $park;
    public bool $showNotification = false;

    public function mount(Park $park)
    {
        $generalCookieName = 'park_crowd_last_logged';
        $specificCookieName = 'park_crowd_logged_' . $park->id;

        $lastLoggedParkId = request()->cookie($generalCookieName);
        $alreadyLoggedThisPark = request()->cookie($specificCookieName);

        $ip = request()->ip();
        if (in_array($ip, ['127.0.0.1', '::1'])) {
            $ip = '8.8.8.8';
        }

        // Loggen wenn:
        // 1. Es der erste Besuch überhaupt ist ODER
        // 2. Ein anderer Park besucht wird ODER
        // 3. Dieser Park noch nicht in den letzten 24h besucht wurde
        if (!$lastLoggedParkId || $lastLoggedParkId != $park->id || !$alreadyLoggedThisPark) {
            $country = $city = null;
            $lat = $lon = null;

            try {
                $reader = new Reader(storage_path('app/geo/GeoLite2-City.mmdb'));
                $record = $reader->city($ip);
                $country = $record->country->name;
                $city = $record->city->name;
                $lat = $record->location->latitude;
                $lon = $record->location->longitude;
            } catch (Exception $e) {
                logger()->info('GeoIP nicht verfügbar: ' . $e->getMessage());
            }

            ParkCrowdReport::create([
                'park_id'     => $park->id,
                'crowd_level' => null,
                'comment'     => null,
                'country'     => $country,
                'city'        => $city,
                'latitude'    => $lat,
                'longitude'   => $lon,
                'ip'          => $ip,
            ]);

            // Setze beide Cookies
            cookie()->queue(cookie($generalCookieName, $park->id, 1440)); // Merke zuletzt besuchten Park
            cookie()->queue(cookie($specificCookieName, true, 1440)); // Merke Besuch dieses Parks

            // Nur dann Notification anzeigen
            $this->showNotification = true;
        }
    }

    public function render()
    {
        return view('livewire.frontend.park-crowd-intro-component');
    }
}
