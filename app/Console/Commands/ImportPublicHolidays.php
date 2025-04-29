<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ImportPublicHolidays extends Command
{
    protected $signature = 'holidays:import {year?}';
    protected $description = 'Importiert Feiertage von Nager.Date in die Datenbank, automatisch für alle Länder aus den Parks.';

    // 🛠 Mapping Land -> Country Code (ISO 2-Letter)
    private array $countryMappings = [
        'Germany' => 'DE',
        'Deutschland' => 'DE',
        'United States' => 'US',
        'USA' => 'US',
        'United states' => 'US',
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
        $year = $this->argument('year') ?? now()->year;

        $this->info("Starte Import der Feiertage für {$year}...");

        // Alle Länder aus Parks ermitteln
        $countries = DB::table('parks')
            ->whereNotNull('country')
            ->distinct()
            ->pluck('country')
            ->map(fn($country) => ucfirst(strtolower($country))) // normalize
            ->unique()
            ->toArray();

        if (empty($countries)) {
            $this->error('Keine Länder gefunden.');
            return 1;
        }

        $this->info('Gefundene Länder: ' . implode(', ', $countries));

        $unmappedCountries = [];

        foreach ($countries as $countryName) {
            $countryCode = $this->countryMappings[$countryName] ?? null;

            if (!$countryCode) {
                $this->warn("⚠️  Kein Mapping für {$countryName} gefunden. Überspringe...");
                $unmappedCountries[] = $countryName;
                continue;
            }

            $this->info("Importiere Feiertage für {$countryName} ({$countryCode})...");

            $response = Http::timeout(20)->get("https://date.nager.at/api/v3/PublicHolidays/{$year}/{$countryCode}");

            if (!$response->successful()) {
                $this->error("Fehler beim Abrufen der Feiertage für {$countryName}.");
                continue;
            }

            $holidays = $response->json();

            foreach ($holidays as $holiday) {
                DB::table('public_holidays')->updateOrInsert(
                    [
                        'date' => $holiday['date'],
                        'country_code' => $holiday['countryCode'],
                    ],
                    [
                        'local_name' => $holiday['localName'],
                        'name' => $holiday['name'],
                        'global' => $holiday['global'],
                        'fixed' => $holiday['fixed'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            $this->info(count($holidays) . " Feiertage für {$countryName} importiert.");
        }

        if (!empty($unmappedCountries)) {
            $this->warn('⚠️  Folgende Länder konnten NICHT gemappt werden:');
            foreach ($unmappedCountries as $unmapped) {
                $this->warn("- {$unmapped}");
            }
        }

        $this->info('✅ Import abgeschlossen.');
        return 0;
    }
}
