<?php

namespace App\Livewire\Frontend\Parks;

use App\Models\Park;
use Livewire\Component;

class ParkMap extends Component
{
    public $parks = [];
    public $bounds = [];
    public $suche = '';
    public $land = '';
    public $status = 'alle'; // 'alle', 'open', 'closed', 'unknown'

    protected $listeners = [
        'sucheAktualisiert' => 'setSuche',
        'filterAktualisiert' => 'updateFilter',
    ];

    public function mount()
    {
        $this->loadFilteredParks();
    }

    public function setSuche($wert)
    {
        $this->suche = trim($wert);
        $this->loadFilteredParks();
    }

    public function updateFilter($daten)
    {
        $this->suche = trim($daten['suche'] ?? '');
        $this->land = trim($daten['land'] ?? '');
        $this->status = trim($daten['status'] ?? 'alle');
        $this->loadFilteredParks();
    }

    public function loadFilteredParks()
    {
        $heute = now()->toDateString();

        $query = Park::whereNotNull('latitude')
                     ->whereNotNull('longitude');

        if ($this->suche) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->suche . '%')
                  ->orWhere('location', 'like', '%' . $this->suche . '%')
                  ->orWhere('country', 'like', '%' . $this->suche . '%');
            });
        }

        if ($this->land) {
            $query->where('country', 'like', '%' . $this->land . '%');
        }

        if ($this->status === 'alle') {
            $query->where('status', 'active');
        } else {
            $query->where('status', $this->status);
        }

        $parks = $query->get()
            ->filter(function ($park) {
                return is_numeric($park->latitude) &&
                       is_numeric($park->longitude) &&
                       !is_null($park->name) &&
                       !is_null($park->location) &&
                       trim($park->name) !== '' &&
                       trim($park->location) !== '';
            })
            ->map(function ($park) {
                $oeffnung = $park->openingHoursToday;
                $heuteGeoeffnet = $oeffnung && $oeffnung->open && $oeffnung->close;

                return [
                    'latitude' => floatval($park->latitude),
                    'longitude' => floatval($park->longitude),
                    'name' => addslashes(trim($park->name)),
                    'location' => addslashes(trim($park->country ?? $park->location)),
                    'status' => $heuteGeoeffnet ? 'open' : 'closed',
                    'status_label' => $heuteGeoeffnet ? '🟢 Geöffnet' : '🔴 Geschlossen',
                    'status_class' => $heuteGeoeffnet ? 'text-green-500' : 'text-red-500',
                    'hours' => $heuteGeoeffnet
                        ? \Carbon\Carbon::parse($oeffnung->open)->format('H:i') . ' – ' . \Carbon\Carbon::parse($oeffnung->close)->format('H:i')
                        : 'Heute geschlossen',
                    'logo' => $park->logo ? asset($park->logo) : null,
                    'image' => $park->image ? asset($park->image) : null,
                ];
            })
            ->values();

        $this->bounds = [];
        if ($parks->count() > 0) {
            $parkArray = $parks->toArray();
            $latitudes = array_column($parkArray, 'latitude');
            $longitudes = array_column($parkArray, 'longitude');
            $this->bounds = [
                'minLat' => min($latitudes),
                'maxLat' => max($latitudes),
                'minLng' => min($longitudes),
                'maxLng' => max($longitudes),
            ];
        }

        $this->parks = $parks->toArray();

       // \Log::info('Gefilterte Parks für Karte:', [
       //     'suche' => $this->suche,
       //     'land' => $this->land,
       //     'status' => $this->status,
       //     'parks' => $this->parks,
       //     'count' => count($this->parks),
       //     'bounds' => $this->bounds,
       // ]);

        $this->dispatch('karteAktualisieren', parks: $this->parks, bounds: $this->bounds);
    }

    public function render()
    {
        return view('livewire.frontend.parks.park-map');
    }
}
