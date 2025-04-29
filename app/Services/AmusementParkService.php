<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver; // Oder Imagick\Driver, je nach Setup
use Carbon\Carbon;
use Illuminate\Support\Str;

class AmusementParkService
{
    protected $queueTimesApiUrl = 'https://queue-times.com/parks.json';
    protected $geocodeService;

    public function __construct(GeocodeService $geocodeService)
    {
        $this->geocodeService = $geocodeService;
    }

    public function getQueueTimesParks()
    {
        $response = Http::get($this->queueTimesApiUrl);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception("Failed to fetch parks from Queue-Times: " . $response->body());
    }

    public function importParksToDatabase(bool $withImages = true)
    {
        $queueTimesParks = $this->getQueueTimesParks();

        foreach ($queueTimesParks as $group) {
            foreach ($group['parks'] as $park) {
                // Koordinaten abrufen
                $latitude = $park['latitude'] ?? null;
                $longitude = $park['longitude'] ?? null;

                if (!$latitude || !$longitude) {
                    $coords = $this->getCoordinates($park['name']);
                    $latitude = $coords['lat'];
                    $longitude = $coords['lon'];
                }

                // Location aus country oder Geocode ableiten
                $location = $park['country'] ?? null;
                if (!$location && $latitude && $longitude) {
                    $location = $this->getLocationFromCoordinates($latitude, $longitude);
                }

                // Eindeutige external_id
                $externalId = Str::slug($park['name'] . '-' . $park['id']);

                // Bild von Wikimedia Commons abrufen und speichern
                // $imagePath = $this->downloadAndConvertParkImage($park['name']);
                $imagePath = $withImages ? $this->downloadAndConvertParkImage($park['name']) : null;

                $slug = Str::slug($park['name']);

                DB::table('parks')->updateOrInsert(
                    ['external_id' => $externalId],
                    [
                        'queue_times_id' => $park['id'],
                        'group_id' => $group['id'],
                        'name' => $park['name'],
                        'slug' => $slug,
                        'group_name' => $group['name'],
                        'location' => $location,
                        'country' => $park['country'] ?? null,
                        'continent' => $park['continent'] ?? null,
                        'timezone' => $park['timezone'] ?? null,
                        'status' => 'pending', // <-- geändert!
                        'description' => 'unknown',
                        'image' => $imagePath,
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }

    protected function getCoordinates($parkName)
    {
        try {
            $response = $this->geocodeService->searchByParkName($parkName);
            return [
                'lat' => $response['lat'] ?? null,
                'lon' => $response['lon'] ?? null,
            ];
        } catch (\Exception $e) {
            \Log::error("Failed to fetch coordinates for {$parkName}: " . $e->getMessage());
            return ['lat' => null, 'lon' => null];
        }
    }

    protected function getLocationFromCoordinates($latitude, $longitude)
    {
        try {
            $response = Http::get('https://nominatim.openstreetmap.org/reverse', [
                'lat' => $latitude,
                'lon' => $longitude,
                'format' => 'json',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['address']['city'] ?? $data['address']['town'] ?? $data['address']['country'] ?? null;
            }
        } catch (\Exception $e) {
            \Log::error("Failed to fetch location for coordinates {$latitude}, {$longitude}: " . $e->getMessage());
        }
        return null;
    }

    protected function getParkImageFromWikimedia($parkName)
    {
        $query = urlencode($parkName . ' amusement park');
        $url = "https://commons.wikimedia.org/w/api.php?action=query&list=search&srsearch={$query}&srnamespace=6&format=json";

        try {
            $response = Http::get($url);
            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['query']['search'])) {
                    $imageTitle = $data['query']['search'][0]['title'];
                    return $this->getImageUrlFromWikimediaTitle($imageTitle);
                }
            }
            // Fallback: Suche ohne "amusement park"
            $query = urlencode($parkName);
            $url = "https://commons.wikimedia.org/w/api.php?action=query&list=search&srsearch={$query}&srnamespace=6&format=json";
            $response = Http::get($url);
            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['query']['search'])) {
                    $imageTitle = $data['query']['search'][0]['title'];
                    return $this->getImageUrlFromWikimediaTitle($imageTitle);
                }
            }
            return null;
        } catch (\Exception $e) {
            \Log::error("Failed to fetch Wikimedia image for {$parkName}: " . $e->getMessage());
            return null;
        }
    }

    protected function getImageUrlFromWikimediaTitle($imageTitle)
    {
        $title = urlencode($imageTitle);
        $url = "https://commons.wikimedia.org/w/api.php?action=query&titles={$title}&prop=imageinfo&iiprop=url&format=json";

        try {
            $response = Http::get($url);
            if ($response->successful()) {
                $data = $response->json();
                $pages = $data['query']['pages'];
                $page = reset($pages);
                return $page['imageinfo'][0]['url'] ?? null;
            }
            return null;
        } catch (\Exception $e) {
            \Log::error("Failed to fetch Wikimedia image URL for {$imageTitle}: " . $e->getMessage());
            return null;
        }
    }

    public function downloadAndConvertParkImage($parkName): ?string
    {
        $imageUrl = $this->getParkImageFromWikimedia($parkName);
        if (!$imageUrl) {
            return null;
        }

        try {
            // Bild herunterladen
            $response = Http::get($imageUrl);
            if (!$response->successful()) {
                \Log::error("Failed to download image for {$parkName}: " . $imageUrl);
                return null;
            }

            $imageContent = $response->body();

            // Intervention Image verwenden
            $extension = 'webp';
            $targetWidth = 480;
            $targetHeight = 280;

            //$manager = new ImageManager(new Driver());
            try {
                $manager = new ImageManager(new \Intervention\Image\Drivers\Imagick\Driver());
            } catch (\Exception $e) {
                \Log::warning("Imagick not available, falling back to GD: " . $e->getMessage());
                $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            }


            $image = $manager->read($imageContent);

            $originalWidth = $image->width();
            $originalHeight = $image->height();

            // Nur skalieren, wenn Bild größer ist
            if ($originalWidth > $targetWidth || $originalHeight > $targetHeight) {
                $image->resize($targetWidth, $targetHeight, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Transparentes Canvas erstellen
            $canvas = $manager->create($targetWidth, $targetHeight)->fill('rgba(255,255,255,0)');
            $canvas->place($image, 'center');

            // Speichern
            $filename = 'img/parklogos/parklogo_' . Str::slug($parkName) . '_' . uniqid() . '.' . $extension;
            Storage::disk('public')->put($filename, (string) $canvas->toWebp(quality: 85));

            return '/storage/' . $filename;
        } catch (\Exception $e) {
            \Log::error("Failed to process image for {$parkName}: " . $e->getMessage());
            return null;
        }
    }
}
