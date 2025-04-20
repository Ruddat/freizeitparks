<?php

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use Spatie\Browsershot\Enums\Polling;
use Carbon\Carbon;

class ParkScraperService
{
    public function scrapeHeidePark(): ?array
    {
        $url = 'https://www.heide-park.de/planen/besuch-planen/oeffnungszeiten/';

        try {
            // Verwende Browsershot, um die Seite zu rendern
            $html = Browsershot::url($url)
                ->waitUntilNetworkIdle()
                ->waitForFunction('() => document.querySelector(".calendar__item") !== null', Polling::Mutation, 10000)
                ->bodyHtml();

            // Speichere HTML für Debugging
            file_put_contents(storage_path('heide_park.html'), $html);

            \Log::debug('Heide-Park-Seite erfolgreich gerendert', [
                'url' => $url,
                'html_length' => strlen($html),
            ]);

            $crawler = new Crawler($html);

            // Versuche mehrere mögliche Selektoren
            $possibleSelectors = [
                '.calendar__item',
                '.opening-hours-item',
                '.calendar-item',
                '.opening-hours',
                '.schedule-item',
                '[data-testid="opening-hours"]',
            ];

            $data = [];

            foreach ($possibleSelectors as $selector) {
                $itemCount = $crawler->filter($selector)->count();
                \Log::debug('Gefundene Elemente für Selektor', [
                    'selector' => $selector,
                    'count' => $itemCount,
                ]);

                if ($itemCount > 0) {
                    $crawler->filter($selector)->each(function (Crawler $node) use (&$data) {
                        // Versuche mehrere mögliche Selektoren für Datum und Zeit
                        $dateNode = $node->filter('.calendar__item-date, .date, [data-date], .opening-date');
                        $timeNode = $node->filter('.calendar__item-time, .time, [data-time], .opening-time');

                        \Log::debug('Verarbeite Kalender-Item', [
                            'date_node_count' => $dateNode->count(),
                            'time_node_count' => $timeNode->count(),
                            'date_text' => $dateNode->count() ? $dateNode->text() : null,
                            'time_text' => $timeNode->count() ? $timeNode->text() : null,
                        ]);

                        if ($dateNode->count() && $timeNode->count()) {
                            $date = $this->normalizeDate($dateNode->text());
                            if ($date) {
                                $data[] = [
                                    'date' => $date,
                                    'time' => trim($timeNode->text()),
                                ];
                            } else {
                                \Log::warning('Datumsnormalisierung fehlgeschlagen', [
                                    'input' => $dateNode->text(),
                                ]);
                            }
                        }
                    });
                    break; // Beende Schleife, wenn ein Selektor funktioniert
                }
            }

            if (empty($data)) {
                \Log::warning('Keine Kalenderdaten auf der Heide-Park-Seite gefunden', [
                    'url' => $url,
                    'html_snippet' => substr($html, 0, 500),
                ]);
                return null;
            }

            \Log::debug('Gescrapte Daten', ['data' => $data]);

            return $data;

        } catch (\Exception $e) {
            \Log::error('Fehler beim Scrapen mit Browsershot', [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    protected function normalizeDate(string $input): ?string
    {
        \Log::debug('Normalisiere Datum', ['input' => $input]);

        // Unterstützt Formate wie "Mo, 01.04.", "01.04.2025", "01.04.", "2025-04-01"
        $input = trim(preg_replace('/[a-zA-Z,]\s*/', '', $input)); // Entferne Wochentage und Kommas
        if (preg_match('/(\d{2})\.(\d{2})\.(\d{4})?|(\d{4})-(\d{2})-(\d{2})/', $input, $matches)) {
            if (isset($matches[3])) { // Format: DD.MM.YYYY oder DD.MM.
                $day = $matches[1];
                $month = $matches[2];
                $year = $matches[3] ?? now()->year;
            } else { // Format: YYYY-MM-DD
                $year = $matches[4];
                $month = $matches[5];
                $day = $matches[6];
            }

            try {
                $date = Carbon::createFromFormat('Y-m-d', "$year-$month-$day");
                if ($date->month < now()->month && now()->month >= 10) {
                    $date->addYear(); // Nächstes Jahr, wenn Monat in Zukunft liegt
                }
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                \Log::warning('Ungültiges Datum beim Normalisieren', [
                    'input' => $input,
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        }

        \Log::warning('Datumsformat nicht erkannt', ['input' => $input]);
        return null;
    }
}
