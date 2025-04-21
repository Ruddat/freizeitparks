<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageDownloadService
{
    // Bestehende download-Methode bleibt unverändert
    public function download(string $ziel, string $land, string $folder = 'destinations'): ?string
    {
        return $this->fromUnsplash($ziel, $land, $folder)
            ?? $this->fromPixabay($ziel, $land, $folder);
    }

    public function fromPixabay(string $ziel, string $land, string $folder = 'destinations'): ?string
    {
        try {
            $query = urlencode("{$ziel} {$land}");
            $apiKey = config('services.pixabay.key');

            $response = Http::get('https://pixabay.com/api/', [
                'key' => $apiKey,
                'q' => $query,
                'image_type' => 'photo',
                'orientation' => 'horizontal',
                'safesearch' => 'true',
                'per_page' => 3,
            ]);

            if ($response->successful() && count($response['hits']) > 0) {
                $url = $response['hits'][0]['largeImageURL'] ?? null;
                return $this->saveImage($url, $ziel, $folder);
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    public function fromUnsplash(string $ziel, string $land, string $folder = 'destinations'): ?string
    {
        try {
            $query = urlencode("{$ziel} {$land}");
            $response = Http::get("https://api.unsplash.com/photos/random", [
                'query' => $query,
                'client_id' => config('services.unsplash.key'),
                'orientation' => 'landscape',
            ]);

            if ($response->successful()) {
                $url = $response->json('urls.regular');
                return $this->saveImage($url, $ziel, $folder);
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    // Neue öffentliche Methode zum Speichern eines Bildes von einer URL
    public function saveImageFromUrl(string $url, string $name, string $folder = 'destinations'): ?string
    {
        return $this->saveImage($url, $name, $folder);
    }

    protected function saveImage(string $url, string $ziel, string $folder = 'destinations'): ?string
    {
        try {
            $imageContent = file_get_contents($url);
            $filename = $folder . '/' . Str::slug($ziel) . '-' . Str::random(6) . '.jpg';
            Storage::disk('public')->put($filename, $imageContent);

            $webp = $this->convertToWebp($filename);
            return $webp ?? $filename;
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function convertToWebp(string $relativePath, int $quality = 80): string|false
    {
        $fullPath = storage_path('app/public/' . $relativePath);

        if (!file_exists($fullPath)) {
            return false;
        }

        $webpPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $relativePath);
        $webpFullPath = storage_path('app/public/' . $webpPath);

        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($fullPath);
            $image->toWebp(quality: $quality)->save($webpFullPath);
        } catch (\Exception $e) {
            return false;
        }

        return Storage::disk('public')->exists($webpPath) ? $webpPath : false;
    }

    public function searchImages(string $ziel, string $land, int $limit = 6): array
    {
        $query = urlencode("{$ziel} {$land}");
        $images = [];

        // Unsplash
        try {
            $unsplash = Http::get("https://api.unsplash.com/search/photos", [
                'query' => $query,
                'client_id' => config('services.unsplash.key'),
                'per_page' => ceil($limit / 2),
                'orientation' => 'landscape',
            ]);

            if ($unsplash->successful()) {
                foreach ($unsplash['results'] as $result) {
                    $images[] = $result['urls']['regular'];
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // Pixabay
        try {
            $pixabay = Http::get("https://pixabay.com/api/", [
                'key' => config('services.pixabay.key'),
                'q' => $query,
                'per_page' => ceil($limit / 2),
                'orientation' => 'horizontal',
                'image_type' => 'photo',
                'safesearch' => 'true',
            ]);

            if ($pixabay->successful()) {
                foreach ($pixabay['hits'] as $hit) {
                    $images[] = $hit['largeImageURL'];
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return $images;
    }
}
