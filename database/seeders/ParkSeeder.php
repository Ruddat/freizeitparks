<?php

namespace Database\Seeders;

use App\Models\Park;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class ParkSeeder extends Seeder
{
    public function run(): void
    {
        $länder = [
            'Deutschland', 'Frankreich', 'Niederlande', 'Italien', 'Spanien', 'Belgien',
            'Österreich', 'Schweiz', 'Polen', 'Dänemark'
        ];

        $namen = [
            'Freizeitpark', 'Erlebniswelt', 'Abenteuerland', 'Funpark', 'Themenpark',
            'Wunderland', 'Traumland', 'Pirateninsel', 'Magic Kingdom', 'Action World'
        ];

        $orte = [
            'Rust' => [48.2667, 7.7333],
            'Paris' => [48.8566, 2.3522],
            'Amsterdam' => [52.3676, 4.9041],
            'Rom' => [41.9028, 12.4964],
            'Barcelona' => [41.3851, 2.1734],
            'Brüssel' => [50.8503, 4.3517],
            'Wien' => [48.2082, 16.3738],
            'Zürich' => [47.3769, 8.5417],
            'Krakau' => [50.0647, 19.9450],
            'Kopenhagen' => [55.6761, 12.5683],
        ];

        $länderList = array_values($länder);
        $orteKeys = array_keys($orte);
        $statusVarianten = ['geöffnet', 'geschlossen'];

        // Unsplash API-Schlüssel aus .env
        $unsplashApiKey = env('UNSPLASH_API_KEY', 'B9bRymPLsaH_-pqFTMZD87GLlttYmoHV2y9BcVi98m8');

        // Hole Freizeitpark-Bilder von Unsplash
        $response = Http::withHeaders([
            'Authorization' => 'Client-ID ' . $unsplashApiKey,
        ])->get('https://api.unsplash.com/search/photos', [
            'query' => 'amusement park rollercoaster',
            'per_page' => 50,
        ]);

        $images = $response->successful() ? collect($response->json()['results'])->pluck('urls.regular')->toArray() : [];
        \Log::info('Unsplash Bilder:', ['count' => count($images), 'images' => $images]);

        $parks = [];

        for ($i = 1; $i <= 50; $i++) {
            $ortIndex = array_rand($orteKeys);
            $ortName = $orteKeys[$ortIndex];
            $koord = $orte[$ortName];

            $lat = $koord[0] + mt_rand(-1000, 1000) / 100000;
            $lng = $koord[1] + mt_rand(-1000, 1000) / 100000;

            $name = $namen[array_rand($namen)] . ' ' . $i;
            $location = $ortName . ', ' . $länder[$ortIndex];
            $status = $statusVarianten[array_rand($statusVarianten)];

            // Wähle ein Bild oder Fallback
            $image = !empty($images) ? $images[array_rand($images)] : '/images/park-placeholder.jpg';

            $parks[] = [
                'name' => $name,
                'location' => $location,
                'status' => $status,
                'image' => $image,
                'latitude' => $lat,
                'longitude' => $lng,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Lösche alte Parks, um Duplikate zu vermeiden
        Park::truncate();
        Park::insert($parks);
    }
}
