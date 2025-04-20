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

    public function mount(Park $park)
    {
        $ip = request()->ip();
        if (in_array($ip, ['127.0.0.1', '::1'])) {
            $ip = '8.8.8.8';
        }

        $exists = ParkCrowdReport::where('park_id', $park->id)
            ->whereDate('created_at', today())
            ->where('comment', null)
            ->where('ip', $ip) // optional, falls du `ip` speicherst
            ->exists();

        if (! $exists) {
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
            ]);
        }
    }

    public function render()
    {
        return view('livewire.frontend.park-crowd-intro-component');
    }
}
