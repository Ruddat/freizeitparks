<?php

namespace App\Console\Commands;

use App\Models\Park;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\SeoTextGeneratorService;

class GenerateSeoTextCommand extends Command
{
    protected $signature = 'parks:generate-seo {--force : Erneut generieren, auch wenn bereits vorhanden}';

    protected $description = 'Generiert SEO-Texte für alle Parks (einmalig oder erzwungen)';

    public function handle()
    {
        $generator = app(SeoTextGeneratorService::class);

        $query = Park::where('status', 'active');

        if (! $this->option('force')) {
            $query->whereNull('seo_text');
        }

        $parks = $query->get();

        if ($parks->isEmpty()) {
            $this->info('Alle SEO-Texte sind bereits vorhanden.');
            return Command::SUCCESS;
        }

        $this->info("Generiere SEO-Texte für {$parks->count()} Parks...");

        foreach ($parks as $park) {
            $this->line("\n➤ {$park->name} ({$park->slug})");

            try {
                $text = $generator->generateSeoTextFor($park);

                if ($text) {
                    $park->update(['seo_text' => $text]);
                    $this->info('✅ Text gespeichert');
                } else {
                    $this->warn('⚠️ Kein Text generiert');
                }

            } catch (\Throwable $e) {
                Log::error("SEO-Text Fehler für Park ID {$park->id}", [
                    'error' => $e->getMessage(),
                ]);
                $this->error('❌ Fehler beim Generieren');
            }
        }

        $this->info("\n🎉 Fertig. Du kannst nun die Sitemap aktualisieren, wenn nötig.");

        return Command::SUCCESS;
    }
}
