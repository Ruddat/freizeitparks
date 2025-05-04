<?php

namespace App\Services;

use App\Models\Park;
use Illuminate\Support\Facades\Http;

class SeoTextGeneratorService
{
    public function generateSeoTextFor(Park $park): ?string
    {
        $prompt = <<<EOT
Du bist ein SEO-Experte. Erstelle einen hochwertigen, einzigartigen Text für eine Landingpage über den Freizeitpark "{$park->name}" in {$park->country}.

1. Verfasse einen Einleitungstext (max. 100 Wörter), der Besucher direkt anspricht.
2. Liste 3 Highlights oder Attraktionen auf (falls keine vorhanden, erfinde welche).
3. Füge 2 Fragen mit passenden Antworten als FAQ hinzu.
4. Verwende Keywords wie "Öffnungszeiten", "Attraktionen", "Besuchstipps", "Wartezeiten" usw.
5. Antworte in Markdown-Format ohne Bulletpoints aus dem Prompt.
EOT;

        $response = Http::timeout(30)->withHeaders([
            'Authorization' => 'Bearer ' . config('services.deepinfra.token'),
            'Content-Type' => 'application/json',
        ])->post('https://api.deepinfra.com/v1/inference/mistralai/Mixtral-8x7B-Instruct-v0.1', [
            'input' => "[INST] {$prompt} [/INST]",
            'max_new_tokens' => 800,
            'temperature' => 0.7,
            'top_p' => 0.9,
        ]);

        if (! $response->successful()) {
            return null;
        }

        return trim($response->json('results.0.generated_text'));
    }
}
