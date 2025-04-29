<?php

namespace App\Console\Commands;

use App\Models\Park;
use App\Models\BlogTag;
use App\Models\BlogPost;
use Illuminate\Support\Str;
use App\Models\BlogCategory;
use Illuminate\Support\Carbon;
use App\Models\ParkCrowdReport;
use Illuminate\Console\Command;
use App\Services\IndexNowService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\AmusementParkService;

class GenerateDailyBlogPost extends Command
{
    protected $signature = 'blog:generate-daily-post';
    protected $description = 'Generiert täglich einen neuen Blogpost basierend auf Bewertungen, Kategorien, Feiertagen und Saisons.';

    public function handle()
    {
        $this->info('Starte Generierung eines neuen Blogposts...');

        $park = Park::where('status', 'active')
        ->orderByRaw('COALESCE(last_reviewed_at, created_at) asc')
        ->first();

        if (!$park) {
            $this->error('Kein geeigneter Park gefunden.');
            return 1;
        }

        $park->touch('last_reviewed_at');

        $report = ParkCrowdReport::where('park_id', $park->id)
            ->inRandomOrder()
            ->first();

        if (!$report) {
            $this->error('Keine Bewertung für Park gefunden: ' . $park->name);
            return 1;
        }

        $holiday = $this->getTodayHoliday();
        $season = $this->detectSeason();
        $category = $this->detectRandomCategory();

        $title = $this->generateTitleBasedOnCategory($park, $category, $holiday, $season);
        $slug = Str::slug($title . '-' . now()->format('Y-m-d-His'));
        $seoTitle = $title;
        $seoDescription = $this->generateSeoDescriptionBasedOnCategory($park, $category, $holiday, $season);

        $prompt = $this->generatePromptBasedOnCategory($park, $report, $category);

        $response = Http::timeout(30)->withHeaders([
            'Authorization' => 'Bearer ' . config('services.deepinfra.token'),
            'Content-Type' => 'application/json',
        ])->post('https://api.deepinfra.com/v1/inference/mistralai/Mixtral-8x7B-Instruct-v0.1', [
            'input' => "[INST] {$prompt} [/INST]",
            'max_new_tokens' => 800,
            'temperature' => 0.7,
            'top_p' => 0.9,
        ]);

        if (!$response->successful()) {
            \Log::error('DeepInfra Fehler', ['response' => $response->body()]);
            return 1;
        }

        $content = $response->json()['results'][0]['generated_text'] ?? null;
        if (!$content || strlen(strip_tags($content)) < 100) {
            $this->error('Leere oder zu kurze Antwort.');
            return 1;
        }

        $dbCategory = BlogCategory::firstOrCreate(
            ['name' => $category['name']],
            ['slug' => $category['slug']]
        );

        $amusementService = app(AmusementParkService::class);
        $image = $amusementService->downloadAndConvertParkImage($park->name);

        if (!$image) {
            $image = asset('storage/img/fallback/park-default.webp');
        }

        $post = BlogPost::create([
            'title' => $title,
            'slug' => $slug,
            'excerpt' => Str::limit(strip_tags($content), 200),
            'content' => $content,
            'featured_image' => $image,
            'category_id' => $dbCategory->id,
            'publish_start' => now(),
            'publish_end' => now()->addMonths(3),
            'seo_title' => $seoTitle,
            'seo_description' => $seoDescription,
            'status' => 'published',
        ]);

        $tags = [
            $park->name,
            'Freizeitpark',
            'Erfahrungsbericht',
            now()->year . ' Saison',
        ];

        $tagIds = collect($tags)->map(function ($name) {
            return BlogTag::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name)]
            )->id;
        });

        $post->tags()->syncWithoutDetaching($tagIds);


        // ✅ IndexNow aufrufen
        app(IndexNowService::class)->ping(url(route('blog.show', $post->slug)));

        $park->update(['last_reviewed_at' => now()]);

        $this->info("Blogpost erstellt: {$post->title}");

        return 0;
    }

    private function getTodayHoliday()
    {
        return DB::table('public_holidays')
            ->whereDate('date', now())
            ->first();
    }

    private function sanitizeHolidayName($name): string
    {
        if (!preg_match('/^[\p{Latin}\s0-9\-\,\.]+$/u', $name)) {
            return 'Feiertag';
        }

        return $name;
    }

    private function detectSeason(): ?string
    {
        return match (now()->month) {
            12 => 'christmas',
            10 => 'halloween',
            11 => 'winter',
            6, 7, 8 => 'summer',
            9 => 'autumn',
            default => null,
        };
    }

    private function detectRandomCategory(): array
    {
        return collect([
            ['name' => 'Park-Erfahrungen', 'slug' => 'park-erfahrungen'],
            ['name' => 'Saisonstarts', 'slug' => 'saisonstarts'],
            ['name' => 'Attraktionen', 'slug' => 'attraktionen'],
            ['name' => 'Freizeitpark Angebote', 'slug' => 'freizeitpark-angebote'],
        ])->random();
    }

    private function generateTitleBasedOnCategory($park, $category, $holiday, $season): string
    {
        $year = now()->year;

        if ($holiday) {
            $clean = $this->sanitizeHolidayName($holiday->local_name);
            if ($clean !== 'Feiertag') {
                return "{$clean} {$year} im {$park->name} 🎉";
            }
        }

        return match($category['slug']) {
            'saisonstarts' => "Saisonstart {$year} im {$park->name} 🎢☀️",
            'attraktionen' => "Attraktions-Highlight im {$park->name} 🎡🎠",
            'freizeitpark-angebote' => "Top-Angebote {$year} im {$park->name} 💰🎟️",
            'park-erfahrungen' => "Freizeitspaß pur im {$park->name} 🎢🎉",
            default => "Freizeitspaß im {$park->name} 🎢",
        };
    }

    private function generateSeoDescriptionBasedOnCategory($park, $category, $holiday, $season): string
    {
        return match($category['slug']) {
            'saisonstarts' => "Entdecke den neuen Saisonstart {$park->name}: neue Attraktionen und Highlights für {$season}!",
            'attraktionen' => "Erlebe die besten Attraktionen im {$park->name} – von Achterbahnen bis Abenteuer!",
            'freizeitpark-angebote' => "Jetzt sparen im Freizeitpark {$park->name} – Rabatte, Gutscheine, Highlights.",
            'park-erfahrungen' => "Unsere frischen Eindrücke aus dem {$park->name} – mit Tipps, Bewertung & mehr.",
            default => "Erlebe Spaß & Abenteuer im Freizeitpark {$park->name}.",
        };
    }

    private function generatePromptBasedOnCategory($park, $report, $category): string
    {
        $base = <<<EOT
Erstelle einen Blogartikel auf Deutsch (ca. 300–350 Wörter) über einen Besuch im Freizeitpark "{$park->name}".

Bitte liefere ausschließlich validen HTML-Body mit <h2>, <p>, <ul><li> etc., ohne <html> oder <head>. Verwende Emojis passend.
EOT;

        return match($category['slug']) {
            'saisonstarts' => $base . "<h2>Saisonstart</h2> Was ist neu? Welche Highlights? Wie ist die Stimmung?",
            'attraktionen' => $base . "<h2>Attraktion entdecken</h2> Stelle eine besondere Attraktion im Fokus.",
            'freizeitpark-angebote' => $base . "<h2>Sparen & Angebote</h2> Zeige aktuelle Deals und Tipps zum Sparen.",
            default => $base . "<h2>Bewertung</h2> Nutze folgende Werte: Besucherandrang {$report->crowd_level}/5, Theming {$report->theming}/5, Sauberkeit {$report->cleanliness}/5, Gastronomie {$report->gastronomy}/5, Attraktivität {$report->attractiveness}/5. Kommentar: \"{$report->comment}\".",
        };
    }
}
