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
        $reports = ParkCrowdReport::where('park_id', $this->park->id)->get();
        $anzahl = $reports->count();

        // Durchschnittswerte je Metrik
        $avgCrowd   = $reports->avg('crowd_level') ?: 0;
        $avgTheming = $reports->avg('theming') ?: 0;
        $avgClean   = $reports->avg('cleanliness') ?: 0;
        $avgGastro  = $reports->avg('gastronomy') ?: 0;
        $avgService = $reports->avg('service') ?: 0;
        $avgAttra   = $reports->avg('attractiveness') ?: 0;

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

        // Kategorien oben (nur 4)
        $kategorien = [
            ['value' => $fmt($avgTheming), 'label' => 'Themenbereich', 'color' => '#4646e6'],
            ['value' => $fmt($avgClean),   'label' => 'Sauberkeit',     'color' => '#3d77f3'],
            ['value' => $fmt($avgGastro),  'label' => 'Gastronomie',    'color' => '#d23ba8'],
            ['value' => $fmt($avgService), 'label' => 'Service',        'color' => '#f5c12b'],
        ];

        // Neuste 3 Kommentare
        $latestComments = $reports->whereNotNull('comment')
            ->sortByDesc('created_at')
            ->take(3);

        $kommentare = $latestComments->map(fn($r) => [
            'stars' => (int) round(collect([$r->theming, $r->cleanliness, $r->gastronomy, $r->service])->avg()),
            'date'  => $r->created_at->format('d. M. Y'),
            'text'  => $r->comment,
        ])->values()->toArray();

        // Alle Kommentare für Modal
        $allComments = $reports->whereNotNull('comment')->sortByDesc('created_at');

        // Gesamtdurchschnitt über alle 6 Metriken
        $gesamtAvg = $fmt(collect([$avgCrowd, $avgTheming, $avgClean, $avgGastro, $avgService, $avgAttra])->avg());

        return view('livewire.frontend.park-bewertungen-component', compact(
            'gesamtAvg', 'anzahl', 'kategorien', 'avgCrowd', 'avgTheming', 'avgClean', 'avgGastro', 'avgService', 'avgAttra', 'crowdColor', 'kommentare', 'allComments'
        ));
    }
}
