<?php

namespace App\Livewire;

use App\Models\Park;
use Livewire\Component;
use Livewire\Attributes\On;

class ParkSuche extends Component
{
    public $suche = '';
    public $isLoading = false;
    public $suggestions = [];

    public function updatedSuche()
    {
        if (strlen($this->suche) >= 2) {
            $this->suggestions = Park::query()
                ->where('name', 'like', '%' . $this->suche . '%')
                ->orWhere('location', 'like', '%' . $this->suche . '%')
                ->orWhere('country', 'like', '%' . $this->suche . '%')
                ->take(5)
                ->get()
                ->map(function ($park) {
                    return [
                        'name' => $park->name,
                        'location' => $park->location,
                        'country' => $park->country
                    ];
                })
                ->toArray();
        } else {
            $this->suggestions = [];
        }

        $this->dispatch('sucheAktualisiert', $this->suche);
        $this->dispatch('scrollToParks');
    }

    public function selectSuggestion($index)
    {
        if (isset($this->suggestions[$index])) {
            $this->suche = $this->suggestions[$index]['name'];
            $this->suggestions = [];
            $this->dispatch('sucheAktualisiert', $this->suche);
            $this->dispatch('scrollToParks');
        }
    }

    public function useCurrentLocation()
    {
        $this->isLoading = true;
        $this->dispatch('get-user-location');
        $this->dispatch('alert',
            message: 'Standort wird abgefragt, bitte warte einen Moment...',
            type: 'info'
        );
    }

    #[On('userLocationReceived')]
    public function handleLocationReceived($coords = [])
    {
        $this->isLoading = false;
        if (!isset($coords['lat'], $coords['lng'])) {
            $this->dispatch('alert',
                message: 'Standort konnte nicht abgefragt werden. Bitte erlaube die Standortfreigabe in deinem Browser.',
                type: 'error'
            );
        } else {
            $this->dispatch('alert',
                message: 'Standort erfolgreich erkannt, Parks in deiner Nähe werden geladen.',
                type: 'success'
            );
        }
    }

    public function render()
    {
        return view('livewire.park-suche');
    }
}
