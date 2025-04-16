<?php

namespace App\Livewire\Frontend;

use App\Models\Park;
use Livewire\Component;

class ParkListeMap extends Component
{
    public $suche = '';
    public $land = '';
    public $status = 'alle';

    public function render()
    {
        $query = Park::query();

        if ($this->suche) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->suche . '%')
                  ->orWhere('location', 'like', '%' . $this->suche . '%');
            });
        }

        if ($this->land) {
            $query->where('location', 'like', '%' . $this->land . '%');
        }

        if ($this->status !== 'alle') {
            $query->where('status', $this->status);
        }

        $parks = $query->get();

        // Alle Länder dynamisch extrahieren
        $laender = Park::selectRaw("SUBSTRING_INDEX(location, ',', -1) as land")
            ->groupBy('land')
            ->pluck('land')
            ->map(fn($land) => trim($land))
            ->sort()
            ->values();

        return view('livewire.frontend.park-liste-map', [
            'parks' => $parks,
            'laender' => $laender,
        ]);
    }
}
