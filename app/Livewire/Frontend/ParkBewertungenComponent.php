<?php
// app/Livewire/Frontend/ParkBewertungenComponent.php

namespace App\Livewire\Frontend;

use Livewire\Component;
use App\Models\ParkCrowdReport;

class ParkBewertungenComponent extends Component
{
    public $park;

    public function mount($park)
    {
        $this->park = $park;
    }

    public function render()
    {
        // Alle Berichte für den Park
        $allReports = ParkCrowdReport::where('park_id', $this->park->id)->get();

        // Nur vollständige Bewertungen (außer crowd_level darf nichts null sein)
        $completeReports = $allReports->filter(function ($r) {
            return !is_null($r->theming) &&
                   !is_null($r->cleanliness) &&
                   !is_null($r->gastronomy) &&
                   !is_null($r->service) &&
                   !is_null($r->attractiveness);
        });

        $anzahl = $completeReports->count();

        // Durchschnittswerte
        $avgCrowd   = $allReports->avg('crowd_level') ?: 0; // Besucher-Dichte: alle Reports
        $avgTheming = $completeReports->avg('theming') ?: 0;
        $avgClean   = $completeReports->avg('cleanliness') ?: 0;
        $avgGastro  = $completeReports->avg('gastronomy') ?: 0;
        $avgService = $completeReports->avg('service') ?: 0;
        $avgAttra   = $completeReports->avg('attractiveness') ?: 0;

        // Farbskala für Andrang
        if ($avgCrowd <= 2) {
            $crowdColor = '#4caf50';
        } elseif ($avgCrowd <= 4) {
            $crowdColor = '#ffeb3b';
        } else {
            $crowdColor = '#f44336';
        }

        // Formatierung Deutsch
        $fmt = fn($v) => number_format($v, 1, ',', '');

        // Kategorien (nur 4)
        $kategorien = [
            ['value' => $fmt($avgTheming), 'label' => 'Themenbereich', 'color' => '#4646e6'],
            ['value' => $fmt($avgClean),   'label' => 'Sauberkeit',     'color' => '#3d77f3'],
            ['value' => $fmt($avgGastro),  'label' => 'Gastronomie',    'color' => '#d23ba8'],
            ['value' => $fmt($avgService), 'label' => 'Service',        'color' => '#f5c12b'],
        ];

        // Neuste 3 Kommentare aus vollständigen Bewertungen
        $latestComments = $completeReports->whereNotNull('comment')
            ->sortByDesc('created_at')
            ->take(3);

        $kommentare = $latestComments->map(fn($r) => [
            'stars' => (int) round(collect([$r->theming, $r->cleanliness, $r->gastronomy, $r->service])->avg()),
            'date'  => $r->created_at->format('d. M. Y'),
            'text'  => $r->comment,
        ])->values()->toArray();

        // Alle Kommentare für Modal (vollständige Reports mit Kommentar)
        $allComments = $completeReports->whereNotNull('comment')->sortByDesc('created_at');

        // Gesamtdurchschnitt über 6 Metriken
        $gesamtAvg = $fmt(collect([$avgCrowd, $avgTheming, $avgClean, $avgGastro, $avgService, $avgAttra])->avg());

        return view('livewire.frontend.park-bewertungen-component', compact(
            'gesamtAvg', 'anzahl', 'kategorien', 'avgCrowd', 'avgTheming', 'avgClean', 'avgGastro', 'avgService', 'avgAttra', 'crowdColor', 'kommentare', 'allComments'
        ));
    }
}
