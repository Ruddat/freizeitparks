<?php

namespace App\Livewire\Frontend\Parks;

use App\Models\Park;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Pagination\LengthAwarePaginator;

class ParkListe extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $suche = '';
    public $land = '';
    public $status = 'alle'; // 'alle', 'open', 'closed', 'unknown'

    public ?float $userLat = null;
    public ?float $userLng = null;
    public int $radiusKm = 250;
    public $flippedStates = [];

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
            $this->resetPage();
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
        $this->resetPage();
    }

    public function resetFilter()
    {
        $this->suche = '';
        $this->land = '';
        $this->status = 'alle';
        $this->userLat = null;
        $this->userLng = null;
        $this->resetPage();
        $this->dispatch('filterAktualisiert', [
            'suche' => $this->suche,
            'land' => $this->land,
            'status' => $this->status,
        ]);
    }

    public function updated($property)
    {
        $this->resetPage();
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

        $query->where('status', 'active');

        if ($this->userLat && $this->userLng) {
            $query->select('*')
                  ->selectRaw(
                      '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                      [$this->userLat, $this->userLng, $this->userLat]
                  )
                  ->having('distance', '<=', $this->radiusKm)
                  ->orderBy('distance');
        }

        $alleLaender = Park::whereNotNull('country')
            ->distinct()
            ->pluck('country')
            ->map(fn($land) => trim($land))
            ->sort()
            ->values();

        // Nutze Livewire's eingebaute Pagination
        $parks = $query->paginate(9);

        // Dynamische Öffnungsstatus-Filter nach der Pagination
        if (in_array($this->status, ['open', 'closed', 'unknown'])) {
            $items = $parks->getCollection()->filter(fn($park) => $park->opening_status === $this->status);
            $parks->setCollection($items);
        }

        logger('📍 Gefilterte Parks:', ['count' => $parks->count(), 'status' => $this->status]);

        return view('livewire.frontend.parks.park-liste', [
            'parks' => $parks,
            'laender' => $alleLaender,
        ]);
    }
}
