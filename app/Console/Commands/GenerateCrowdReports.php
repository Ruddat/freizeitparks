<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Park;
use Illuminate\Support\Carbon;

class GenerateCrowdReports extends Command
{
    protected $signature = 'parks:generate-crowd-reports {--max=10}';
    protected $description = 'Generiert automatisch neue Crowd-Reports für aktive Freizeitparks.';

    private array $countryMappings = [
        'Germany' => 'DE',
        'Deutschland' => 'DE',
        'United States' => 'US',
        'United states' => 'US',
        'USA' => 'US',
        'Canada' => 'CA',
        'China' => 'CN',
        'Belgium' => 'BE',
        'Austria' => 'AT',
        'France' => 'FR',
        'Netherlands' => 'NL',
        'England' => 'GB',
        'United Kingdom' => 'GB',
        'Italy' => 'IT',
        'Denmark' => 'DK',
        'Japan' => 'JP',
        'South Korea' => 'KR',
        'Brazil' => 'BR',
        'Poland' => 'PL',
        'Sweden' => 'SE',
        'Spain' => 'ES',
        'Mexico' => 'MX',
        'Hong Kong' => 'HK',
    ];

    public function handle()
    {
        $this->info('Starte die Generierung von Crowd-Reports...');

        $successfulParks = 0;
        $failedParks = 0;
        $skippedParks = 0;

        $date = now();
        $maxParks = (int) $this->option('max');

        $parks = Park::whereNotIn('status', ['pending', 'unknown'])
            ->orderBy('last_reviewed_at', 'asc')
            ->limit($maxParks)
            ->get();

        if ($parks->isEmpty()) {
            $this->warn('Keine aktiven Parks gefunden.');
            Log::warning('Keine aktiven Parks gefunden.');
            return 0;
        }

        foreach ($parks as $park) {
            if (empty($park->country)) {
                $this->warn("Kein Country für Park {$park->name}, überspringe...");
                Log::warning("Kein Country für Park {$park->name}, übersprungen.");
                $skippedParks++;
                continue;
            }

            $countryCode = $this->mapCountryNameToCode($park->country);

            if (!$countryCode) {
                $this->warn("Kein Mapping für Country '{$park->country}' (Park {$park->name}), überspringe...");
                Log::warning("Kein Mapping für Country '{$park->country}' (Park {$park->name}), übersprungen.");
                $skippedParks++;
                continue;
            }

            $crowdLevelTarget = $this->determineCrowdLevelTarget($date, $countryCode);
            $anzahlBewertungen = rand(3, 9);

            $prompt = $this->generatePrompt($park->name, $anzahlBewertungen, $crowdLevelTarget, $date, $countryCode);

            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . config('services.deepinfra.token'),
                'Content-Type' => 'application/json',
            ])->post('https://api.deepinfra.com/v1/inference/mistralai/Mixtral-8x7B-Instruct-v0.1', [
                'input' => "[INST] {$prompt} [/INST]",
                'max_new_tokens' => 4000,
                'temperature' => 0.7,
                'top_p' => 0.9,
            ]);

            if (!$response->successful()) {
                $this->error("Fehler bei DeepInfra-API für Park: {$park->name}");
                Log::error("Fehler bei DeepInfra-API für Park: {$park->name}");
                $failedParks++;
                continue;
            }

            $output = $response->json()['results'][0]['generated_text'] ?? null;

            if (!$output) {
                $this->error("Leere Antwort von DeepInfra für Park: {$park->name}");
                Log::error("Leere Antwort von DeepInfra für Park: {$park->name}");
                $failedParks++;
                continue;
            }

            $bewertungen = $this->parseJson($output);

            if (!$bewertungen) {
                $this->warn("Konnte JSON für {$park->name} nicht parsen.");
                Log::error("Konnte JSON für {$park->name} nicht parsen. Antwort: " . $output);
                $failedParks++;
                continue;
            }

            foreach ($bewertungen as $bewertung) {
                DB::table('park_crowd_reports')->insert([
                    'park_id' => $park->id,
                    'crowd_level' => $this->sanitizeRating($bewertung['crowd_level'] ?? $crowdLevelTarget),
                    'theming' => $this->sanitizeRating($bewertung['theming'] ?? rand(3,5)),
                    'cleanliness' => $this->sanitizeRating($bewertung['cleanliness'] ?? rand(3,5)),
                    'gastronomy' => $this->sanitizeRating($bewertung['gastronomy'] ?? rand(3,5)),
                    'service' => $this->sanitizeRating($bewertung['service'] ?? rand(3,5)),
                    'attractiveness' => $this->sanitizeRating($bewertung['attractiveness'] ?? rand(3,5)),
                    'comment' => (!empty($bewertung['comment']) && is_string($bewertung['comment']))
                        ? $this->makeCommentMoreNatural($bewertung['comment'])
                        : $this->generateFallbackComment(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $park->last_reviewed_at = now();
            $park->save();

            $this->info("{$anzahlBewertungen} Bewertungen für '{$park->name}' gespeichert.");
            Log::info("{$anzahlBewertungen} Bewertungen für '{$park->name}' gespeichert.");
            $successfulParks++;
        }

        $this->info('Generierung abgeschlossen.');
        $this->info("Erfolgreiche Parks: {$successfulParks} | Fehlgeschlagene Parks: {$failedParks} | Übersprungene Parks: {$skippedParks}");

        Log::info('Generierung abgeschlossen.', [
            'erfolgreiche_parks' => $successfulParks,
            'fehlgeschlagene_parks' => $failedParks,
            'uebersprungene_parks' => $skippedParks,
        ]);

        return 0;
    }

    private function parseJson(string $text)
    {
        $start = strpos($text, '[');
        $end = strrpos($text, ']');

        if ($start === false || $end === false) {
            return null;
        }

        $jsonString = substr($text, $start, $end - $start + 1);
        $jsonString = trim($jsonString);
        $jsonString = str_replace('\\_', '_', $jsonString);
        $jsonString = str_replace('\\"', '"', $jsonString);
        $jsonString = str_replace('\\\\', '\\', $jsonString);

        $parsed = json_decode($jsonString, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return $parsed;
    }

    private function determineCrowdLevelTarget(Carbon $date, string $countryCode): int
    {
        $dayOfWeek = $date->dayOfWeekIso;

        if ($this->isPublicHoliday($date, $countryCode) || in_array($dayOfWeek, [6,7])) {
            return rand(4, 5);
        }

        return rand(2, 3);
    }

    private function isPublicHoliday(Carbon $date, string $countryCode): bool
    {
        return DB::table('public_holidays')
            ->whereDate('date', $date)
            ->where('country_code', strtoupper($countryCode))
            ->exists();
    }

    private function mapCountryNameToCode(string $countryName): ?string
    {
        $normalized = ucfirst(strtolower(trim($countryName)));
        return $this->countryMappings[$normalized] ?? null;
    }

    private function sanitizeRating($value): int
    {
        if (is_numeric($value) && $value >= 1 && $value <= 5) {
            return (int) $value;
        }

        return rand(3,5);
    }

    private function makeCommentMoreNatural(string $comment): string
    {
        $additions = [
            '',
            ' 😍',
            ' 🔥',
            ' Mega!',
            ' Sehr cool!',
            ' Einfach top!',
            ' War echt super.',
            ' War einfach hammer!',
        ];

        if (rand(1,5) === 1) {
            return rtrim($comment, '.') . $additions[array_rand($additions)];
        }

        return $comment;
    }

    private function generateFallbackComment(): string
    {
        $fallbacks = [
            'Insgesamt ein toller Tag!',
            'Die Erfahrung war gut!',
            'Viele Attraktionen haben Spaß gemacht!',
            'Tolle Stimmung im Park!',
            'Würde den Park wieder besuchen!',
        ];

        return $fallbacks[array_rand($fallbacks)];
    }

    private function generatePrompt(string $parkName, int $anzahlBewertungen, int $crowdLevelTarget, Carbon $date, string $countryCode): string
    {
        $dayOfWeek = $date->dayOfWeekIso;
        $isHoliday = $this->isPublicHoliday($date, $countryCode);

        $promptOptions = [];

        if (in_array($dayOfWeek, [6,7]) || $isHoliday) {
            $promptOptions = [
                "Schreibe {$anzahlBewertungen} kurze Bewertungen über die Wartezeiten und Besucherströme im Freizeitpark '{$parkName}'. Crowd-Level ungefähr {$crowdLevelTarget}. Deutsch, locker, freundlich.",
                "Erstelle {$anzahlBewertungen} Meinungen zur Auslastung und Atmosphäre im Park '{$parkName}', auf Deutsch, locker.",
            ];
        } else {
            $promptOptions = [
                "Formuliere {$anzahlBewertungen} Eindrücke über Attraktionen, Gastronomie und Service im Freizeitpark '{$parkName}'. Deutsch, freundlich, maximal 2 Sätze pro Bewertung.",
                "Verfasse {$anzahlBewertungen} Kurzbewertungen über die Qualität der Attraktionen und den Kundenservice im Park '{$parkName}'.",
            ];
        }

        return $promptOptions[array_rand($promptOptions)]
        . " Antworte ausschließlich auf Deutsch. "
        . "Gib das Ergebnis ausschließlich als JSON-Array zurück, kein Fließtext. "
        . "Die Werte crowd_level, theming, cleanliness, gastronomy, service und attractiveness dürfen nur Zahlen von 1 bis 5 enthalten. "
        . "Jede Bewertung muss ein comment enthalten mit 1–2 freundlichen Sätzen auf Deutsch.";
    }
}
