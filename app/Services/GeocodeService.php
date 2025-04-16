<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeocodeService
{
    protected $nominatimUrl = 'https://nominatim.openstreetmap.org/search';

    public function searchByParkName($parkName)
    {
        $response = Http::get($this->nominatimUrl, [
            'q' => $parkName,
            'format' => 'json',
            'limit' => 1,
        ]);

        if ($response->successful() && count($response->json()) > 0) {
            return [
                'lat' => $response->json()[0]['lat'],
                'lon' => $response->json()[0]['lon'],
            ];
        }

        throw new \Exception("No coordinates found for {$parkName}");
    }
}
