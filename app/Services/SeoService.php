<?php

namespace App\Services;

use App\Models\Park;
use App\Models\StaticPage;
use App\Models\ModSeoMeta;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class SeoService
{
    public function getSeoData($model = null, ?string $customType = null, ?int $customId = null)
    {
        $modelType = $customType ?? get_class($model);
        $modelId = $customId ?? ($model->id ?? 0);
        $siteName = $this->getSiteSetting('site_name', 'Parkverzeichnis.de');
        $defaultImage = $this->formatImageUrl($this->getSiteSetting('site_logo') ?? 'img/default-bg.jpg');

        $cacheKey = "seo_{$modelType}_{$modelId}";
        return Cache::remember($cacheKey, now()->addDays(7), function () use ($model, $modelType, $modelId, $siteName, $defaultImage) {
            $seo = ModSeoMeta::where('model_type', $modelType)
                             ->where('model_id', $modelId)
                             ->first();

            $titleBase = $model ? $this->getModelTitle($model) : 'Parkverzeichnis.de';
            $keywords = $this->generateKeywords($model, $titleBase, $modelType);
            $imageUrl = $model ? $this->getModelImage($model) : $defaultImage;
            $canonicalUrl = $model ? $this->generateCanonicalUrl($model) : url('/');

            if (!$seo) {
                Log::info("Kein SEO-Eintrag für {$modelType} ID: {$modelId} – wird erstellt.");
                $seo = $this->createSeoEntry($modelType, $modelId, $titleBase, $keywords, $imageUrl, $canonicalUrl, $siteName, $model);
            } elseif (!$seo->prevent_override) {
                $this->updateSeoEntry($seo, $titleBase, $keywords, $imageUrl, $canonicalUrl, $siteName, $model);
            }

            return [
                'title' => $seo->title,
                'description' => $seo->description,
                'image' => $seo->image,
                'canonical' => $seo->canonical,
                'extra_meta' => json_decode($seo->extra_meta, true),
                'keywords' => json_decode($seo->keywords, true),
            ];
        });
    }

    private function createSeoEntry($modelType, $modelId, $titleBase, $keywords, $imageUrl, $canonicalUrl, $siteName, $model = null)
    {
        return ModSeoMeta::create([
            'model_type' => $modelType,
            'model_id' => $modelId,
            'title' => "{$titleBase} – {$siteName}",
            'description' => $keywords['description'],
            'image' => $imageUrl,
            'canonical' => $canonicalUrl,
'extra_meta' => json_encode(
    $this->generateDefaultExtraMeta($titleBase, $keywords, $imageUrl, $canonicalUrl, $siteName, $model)
),
            'keywords' => json_encode($keywords),
        ]);
    }

    private function updateSeoEntry($seo, $titleBase, $keywords, $imageUrl, $canonicalUrl, $siteName, $model = null)
    {
        $newTitle = "{$titleBase} – {$siteName}";
        $newDescription = $keywords['description'];
        $needsUpdate = $seo->title !== $newTitle || $seo->description !== $newDescription || $seo->image !== $imageUrl;

        if ($needsUpdate) {
            $seo->update([
                'title' => $newTitle,
                'description' => $newDescription,
                'image' => $imageUrl,
                'canonical' => $canonicalUrl,
'extra_meta' => json_encode(
    $this->generateDefaultExtraMeta($titleBase, $keywords, $imageUrl, $canonicalUrl, $siteName, $model)
),
                'keywords' => json_encode($keywords),
            ]);
            Log::info("SEO aktualisiert für {$seo->model_type} ID {$seo->model_id}");
        }
    }

    private function generateDefaultExtraMeta($titleBase, $keywords, $imageUrl, $canonicalUrl, $siteName, $model = null)
    {
        $isBlog = $model instanceof \App\Models\BlogPost;

        $meta = [
            'og:title' => "{$titleBase} – {$siteName}",
            'og:description' => $keywords['description'],
            'og:image' => $imageUrl ?: asset('img/default-bg.jpg'),
            'og:url' => $canonicalUrl,
            'og:type' => $isBlog ? 'article' : 'website',
            'og:locale' => 'de_DE',
            'twitter:card' => 'summary_large_image',
            'twitter:title' => "{$titleBase} – {$siteName}",
            'twitter:description' => $keywords['description'],
            'twitter:image' => $imageUrl ?: asset('img/default-bg.jpg'),
        ];

        // ➕ Zusätzliche Tags für Artikel
        if ($isBlog && isset($model->publish_start)) {
            $meta['article:published_time'] = $model->publish_start->toAtomString();
            $meta['article:author'] = 'Parkverzeichnis.de';
        }

        return $meta;
    }

    private function generateCanonicalUrl($model)
    {
        if (!empty($model->url) && filter_var($model->url, FILTER_VALIDATE_URL)) {
            return $model->url;
        }

        if ($model instanceof Park && Route::has('parks.show')) {
            return route('parks.show', $model->slug);
        }

        if ($model instanceof StaticPage && Route::has('static.show')) {
            return route('static.show', $model->slug);
        }

        return url()->current();
    }

    private function getModelTitle($model)
    {
        return $model->title ?? $model->name ?? 'Freizeitpark';
    }

    private function getModelImage($model)
    {
        // Reihenfolge: individuell definiertes SEO-Bild, dann typische Felder
        $image = $model->seo_image
            ?? $model->featured_image
            ?? $model->logo
            ?? $model->image
            ?? null;

        // Wenn kein Bild gefunden, gib Standardbild zurück
        return $this->formatImageUrl($image ?: 'img/default-bg.jpg');
    }

    private function formatImageUrl($path)
    {
        if (Str::startsWith($path, 'http')) {
            return $path;
        }

        if (Str::startsWith($path, 'storage/')) {
            return asset($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return asset('storage/' . ltrim($path, '/'));
        }

        return asset('img/default-bg.jpg');
    }

    private function generateKeywords($model, $title, $modelType)
    {
        $nextYear = now()->year + 1;
        $defaultKeywords = $this->getSiteSetting('default_meta_keywords', ['freizeitparks', 'familienausflug', 'parkverzeichnis']);

        $rawDescription = $model->description ?? null;

        if ($rawDescription) {
            $plainText = strip_tags($rawDescription);
            $shortDescription = Str::limit(trim($plainText), 160, '...');
        } else {
            $shortDescription = "Alle Freizeitparks in Europa entdecken – mit Bewertungen, Öffnungszeiten & Videos.";
        }

        return [
            'main' => $title,
            'description' => $shortDescription,
            'tags' => array_merge([$title, "{$title} Freizeitparks"], (array) $defaultKeywords),
            'nextYear' => $nextYear,
        ];
    }

    public function getSiteSetting($key, $default = null)
    {
        $setting = DB::table('mod_site_settings')->where('key', $key)->first();

        if (!$setting) return $default;

        return match ($setting->type) {
            'json' => json_decode($setting->value, true),
            'boolean' => (bool) $setting->value,
            'file', 'string' => $setting->value,
            default => $setting->value,
        };
    }

    public function getSocialProfiles(): array
    {
        return array_values(array_filter([
            $this->getSiteSetting('facebook_url'),
            $this->getSiteSetting('instagram_url'),
            $this->getSiteSetting('youtube_url'),
            $this->getSiteSetting('tiktok_url'),
        ]));
    }

    public function getDefaultSeoForStartpage(): array
    {
        $siteName = $this->getSiteSetting('site_name', 'Parkverzeichnis.de');
        $image = $this->formatImageUrl($this->getSiteSetting('site_logo') ?? 'img/default-bg.jpg');

        return [
            'title' => "$siteName – Freizeitparks entdecken",
            'description' => 'Alle Freizeitparks in Deutschland & Europa auf einen Blick – mit Öffnungszeiten, Bewertungen, Videos & mehr!',
            'canonical' => url('/'),
            'image' => $image,
            'keywords' => [
                'main' => 'Freizeitparks entdecken',
                'description' => 'Alle Freizeitparks in Europa mit Infos zu Öffnungszeiten, Attraktionen und Bewertungen.',
                'tags' => $this->getSiteSetting('default_meta_keywords', ['freizeitparks', 'familienausflug', 'parks']),
                'nextYear' => now()->addYear()->year,
            ],
            'extra_meta' => [
                'og:title' => "$siteName – Freizeitparks entdecken",
                'og:description' => 'Entdecke alle Freizeitparks auf einer Karte – mit aktuellen Infos & Bewertungen.',
                'og:image' => $image,
                'og:url' => url('/'),
                'og:type' => 'website',
                'og:locale' => 'de_DE',
                'twitter:card' => 'summary_large_image',
                'twitter:title' => "$siteName – Freizeitparks entdecken",
                'twitter:description' => 'Finde deinen Lieblingspark in Deutschland & Europa.',
                'twitter:image' => $image,
            ],
        ];
    }



}
