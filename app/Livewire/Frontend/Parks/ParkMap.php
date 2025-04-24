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
        $query = Park::whereNotNull('latitude')
                     ->whereNotNull('longitude')
                     ->where('status', 'active');

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

        $parks = $query->get()
            ->filter(function ($park) {
                return is_numeric($park->latitude) &&
                       is_numeric($park->longitude) &&
                       !is_null($park->name) &&
                       !is_null($park->location) &&
                       trim($park->name) !== '' &&
                       trim($park->location) !== '';
            });

        // Dynamischer Öffnungsstatus-Filter nachträglich in PHP
        if (in_array($this->status, ['open', 'closed', 'unknown'])) {
            $parks = $parks->filter(fn($park) => $park->opening_status === $this->status);
        }

        $parks = $parks->map(function ($park) {
            $oeffnung = $park->openingHoursToday;
            $geoeffnet = $park->opening_status === 'open';

            return [
                'latitude' => floatval($park->latitude),
                'longitude' => floatval($park->longitude),
                'name' => addslashes(trim($park->name)),
                'location' => addslashes(trim($park->country ?? $park->location)),
                'status' => $park->opening_status,
                'status_label' => $park->status_label,
                'status_class' => $park->status_class,
                'hours' => $oeffnung && $oeffnung->open && $oeffnung->close
                    ? \Carbon\Carbon::parse($oeffnung->open)->format('H:i') . ' – ' . \Carbon\Carbon::parse($oeffnung->close)->format('H:i')
                    : 'Heute geschlossen',
                'logo' => $park->logo ? asset($park->logo) : null,
                'image' => $park->image ? asset($park->image) : null,
            ];
        })->values();

        // Kartenbereich berechnen
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

        $this->dispatch('karteAktualisieren', parks: $this->parks, bounds: $this->bounds);
    }


    public function render()
    {
        return view('livewire.frontend.parks.park-map');
    }
}
