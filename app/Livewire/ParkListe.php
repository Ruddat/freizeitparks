<?php

namespace App\Livewire;

use App\Models\Park;
use Livewire\Component;
use Livewire\Attributes\On;

class ParkListe extends Component
{
    public $suche = '';
    public $land = '';
    public $status = 'alle'; // 'alle', 'open', 'closed', 'unknown'

    public ?float $userLat = null;
    public ?float $userLng = null;
    public int $radiusKm = 250;

    protected $listeners = [
        'sucheAktualisiert' => 'setSuche'
    ];

    #[On('userLocationReceived')]
    public function setUserLocation($coords = [])
    {
        logger('📍 Rohdaten empfangen:', ['coords' => $coords]);

        if (!is_array($coords) || !isset($coords['lat'], $coords['lng'])) {
            logger('⚠️ Ungültige Standortdaten:', ['coords' => $coords]);
            $this->userLat = null;
            $this->userLng = null;
            return;
        }

        $this->userLat = is_numeric($coords['lat']) ? floatval($coords['lat']) : null;
        $this->userLng = is_numeric($coords['lng']) ? floatval($coords['lng']) : null;

        logger('📍 Standort gesetzt:', ['lat' => $this->userLat, 'lng' => $this->userLng]);

        if ($this->userLat === null || $this->userLng === null) {
            $this->dispatch('alert', ['message' => 'Standort konnte nicht gesetzt werden.', 'type' => 'error']);
        } else {
            $this->dispatch('filterAktualisiert', [
                'suche' => $this->suche,
                'land' => $this->land,
                'status' => $this->status,
            ]);
        }
    }

    public function distanceBetween($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371;
        $lat1 = floatval($lat1);
        $lon1 = floatval($lon1);
        $lat2 = floatval($lat2);
        $lon2 = floatval($lon2);

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function setSuche($wert)
    {
        $this->suche = trim($wert);
    }

    public function resetFilter()
    {
        $this->suche = '';
        $this->land = '';
        $this->status = 'alle';
        // Optional: Standort zurücksetzen
        $this->userLat = null;
        $this->userLng = null;
        $this->dispatch('filterAktualisiert', [
            'suche' => $this->suche,
            'land' => $this->land,
            'status' => $this->status,
        ]);
        // Event für Kartenaktualisierung

    }

    public function updated($property)
    {
        $this->dispatch('filterAktualisiert', [
            'suche' => $this->suche,
            'land' => $this->land,
            'status' => $this->status,
        ]);
    }

    public function render()
    {
        $query = Park::query();

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

        if ($this->status !== 'alle') {
            $query->where('status', $this->status);
        }

        $alleLaender = Park::whereNotNull('country')
            ->distinct()
            ->pluck('country')
            ->map(fn($land) => trim($land))
            ->sort()
            ->values();

        $parks = $query->get();
        logger('📍 Parks nach Filtern:', ['count' => $parks->count()]);

        if ($this->userLat && $this->userLng) {
            $parks = $parks->filter(function ($park) {
                if (!$park->latitude || !$park->longitude) {
                    logger('📍 Park ohne Koordinaten:', ['park' => $park->toArray()]);
                    return false;
                }
                $distance = $this->distanceBetween($this->userLat, $this->userLng, $park->latitude, $park->longitude);
                logger('📍 Entfernung für Park:', [
                    'park' => $park->name,
                    'lat' => $park->latitude,
                    'lng' => $park->longitude,
                    'distance_km' => $distance
                ]);
                return $distance <= $this->radiusKm;
            });
        }

        logger('📍 Finale Parks:', ['count' => $parks->count()]);

        return view('livewire.park-liste', [
            'parks' => $parks,
            'laender' => $alleLaender,
        ]);
    }
}
