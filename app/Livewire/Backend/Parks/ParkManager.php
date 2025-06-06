<?php

namespace App\Livewire\Backend\Parks;

use Carbon\Carbon;
use App\Models\Park;
use Livewire\Component;
use Carbon\CarbonPeriod;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Services\IndexNowService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ParkManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $editingPark = null;
    public $isModalOpen = false;
    public $perPage = 10;
    public $logoUpload;
    public $keywords = '';
    public $imageUpload;
    public $imageUploadPath = 'images/parks/';

    public $sortField = 'name';
    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function mount()
    {
        $this->resetEditingPark();
    }

    public function resetEditingPark()
    {
        $this->editingPark = [
            'id' => null,
            'external_id' => '',
            'queue_times_id' => null,
            'group_id' => null,
            'name' => '',
            'group_name' => '',
            'location' => '',
            'country' => '',
            'continent' => '',
            'timezone' => '',
            'status' => '',
            'image' => '',
            'latitude' => null,
            'longitude' => null,
            'url' => '',
            'video_embed_code' => '',
            'video_url' => '',
            'logo' => '',
            'description' => '',
            'type' => '',
            'created_at' => null,
            'updated_at' => null,
            'street' => '',
            'zip' => '',
            'city' => '',
        ];

        $this->isModalOpen = false;
        $this->logoUpload = null;
        $this->imageUpload = null;
        $this->keywords = '';
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }
    public function updatingLogoUpload() { if ($this->isModalOpen) { $this->dispatch('open-modal'); } }

    public function generateDescription() {
        $this->validate([
            'editingPark.name' => 'required|string|max:255',
            'keywords' => 'nullable|string|max:500',
        ]);

        $parkName = $this->editingPark['name'];
        $keywords = $this->keywords ?: '';
        $prompt = "Erstelle eine detaillierte und ansprechende Beschreibung für den Freizeitpark '$parkName'. Berücksichtige die folgenden Stichworte, falls angegeben: $keywords. Beschreibe Attraktionen, Atmosphäre, Zielgruppe (z. B. Familien, Abenteuerlustige), besondere Merkmale (z. B. Themenbereiche, Shows) und praktische Informationen (z. B. Restaurants, Zugänglichkeit). Die Beschreibung soll maximal 3000 Wörter lang sein, informativ, freundlich und für Besucher attraktiv sein. Verwende HTML-Formatierung (z. B. <h3>, <p>, <ul>) für Struktur.";

        try {
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . config('services.deepinfra.token'),
                'Content-Type' => 'application/json',
            ])->post('https://api.deepinfra.com/v1/inference/mistralai/Mixtral-8x7B-Instruct-v0.1', [
                'input' => "[INST] {$prompt} [/INST]",
                'max_new_tokens' => 4000,
                'temperature' => 0.7,
                'top_p' => 0.9,
            ]);

            $generatedText = $response->json()['results'][0]['generated_text'] ?? '';
            if (empty($generatedText)) {
                session()->flash('error', 'Fehler bei der Generierung: Kein Text empfangen.');
                return;
            }

            $this->editingPark['description'] = $generatedText;
            $this->dispatch('updateJoditEditor', $generatedText);
            session()->flash('success', 'Beschreibung erfolgreich generiert!');

        } catch (\Exception $e) {
            \Log::error('Fehler in generateDescription', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('error', 'Fehler bei der Kommunikation mit der API: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $parks = Park::where('name', 'like', '%' . $this->search . '%')
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate($this->perPage);

        return view('livewire.backend.parks.park-manager', [
            'parks' => $parks,
            'parkTypes' => ['Freizeitpark', 'Wasserpark', 'Zoo', 'Themenpark', 'Andere'],
        ])->extends('backend.layouts.backend')->section('main-content');
    }

    public function edit($id)
    {
        $park = Park::find($id);
        if ($park) {
            $this->editingPark = [
                'id' => $park->id,
                'external_id' => $park->external_id,
                'queue_times_id' => $park->queue_times_id,
                'group_id' => $park->group_id,
                'name' => $park->name,
                'group_name' => $park->group_name,
                'location' => $park->location,
                'street' => $park->street,
                'zip' => $park->zip,
                'city' => $park->city,
                'country' => $park->country,
                'continent' => $park->continent,
                'timezone' => $park->timezone,
                'status' => $park->status,
                'image' => $park->image,
                'latitude' => $park->latitude,
                'longitude' => $park->longitude,
                'url' => $park->url,
                'video_embed_code' => $park->video_embed_code,
                'video_url' => $park->video_url,
                'logo' => $park->logo,
                'description' => $park->description,
                'type' => $park->type,
                'created_at' => optional($park->created_at)->toDateTimeString(),
                'updated_at' => optional($park->updated_at)->toDateTimeString(),
            ];
            $this->isModalOpen = true;
            $this->dispatch('open-modal');
        } else {
            session()->flash('error', 'Park nicht gefunden.');
        }
    }

    public function create() {
        $this->resetEditingPark();
        $this->isModalOpen = true;
        $this->dispatch('open-modal');
    }

    public function update()
    {
        $this->validate([
            'editingPark.external_id' => 'nullable|string|max:255|unique:parks,external_id,' . ($this->editingPark['id'] ?? 'NULL'),
            'editingPark.queue_times_id' => 'nullable|integer',
            'editingPark.group_id' => 'nullable|integer',
            'editingPark.name' => 'required|string|max:255',
            'editingPark.group_name' => 'nullable|string|max:255',
            'editingPark.location' => 'nullable|string|max:255',
            'editingPark.country' => 'nullable|string|max:255',
            'editingPark.continent' => 'nullable|string|max:255',
            'editingPark.timezone' => 'nullable|string|max:255',
            'editingPark.status' => 'required|string|max:255',
            'editingPark.image' => 'nullable|string|max:255',
            'editingPark.latitude' => 'nullable|numeric|between:-90,90',
            'editingPark.longitude' => 'nullable|numeric|between:-180,180',
            'editingPark.url' => 'nullable|url|max:255',
            'editingPark.video_embed_code' => 'nullable|string',
            'editingPark.video_url' => 'nullable|url|max:255',
            'logoUpload' => 'nullable|image|max:2048',
            'imageUpload' => 'nullable|image|max:4096',
            'editingPark.description' => 'nullable|string|max:100000',
            'editingPark.type' => 'nullable|string|max:255',
        ]);

        $data = [
            'external_id' => $this->editingPark['external_id'] ?: null,
            'queue_times_id' => $this->editingPark['queue_times_id'],
            'group_id' => $this->editingPark['group_id'],
            'name' => $this->editingPark['name'],
            'group_name' => $this->editingPark['group_name'],
            'location' => $this->editingPark['location'],
            'street' => $this->editingPark['street'],
            'zip' => $this->editingPark['zip'],
            'city' => $this->editingPark['city'],
            'country' => $this->editingPark['country'],
            'continent' => $this->editingPark['continent'],
            'timezone' => $this->editingPark['timezone'],
            'status' => $this->editingPark['status'],
            'image' => $this->editingPark['image'],
            'latitude' => $this->editingPark['latitude'],
            'longitude' => $this->editingPark['longitude'],
            'url' => $this->editingPark['url'],
            'video_embed_code' => $this->editingPark['video_embed_code'],
            'video_url' => $this->editingPark['video_embed_code'] ? null : $this->editingPark['video_url'],
            'description' => $this->editingPark['description'],
            'type' => $this->editingPark['type'],
        ];

        if ($this->logoUpload) {
            if (!empty($this->editingPark['logo'])) {
                Storage::disk('public')->delete(str_replace('storage/', '', $this->editingPark['logo']));
            }
            $path = $this->logoUpload->store('logos', 'public');
            $data['logo'] = 'storage/' . $path;
        }

        if ($this->imageUpload) {
            if (!empty($this->editingPark['image'])) {
                Storage::disk('public')->delete(str_replace('storage/', '', $this->editingPark['image']));
            }
            $path = $this->imageUpload->store('parks', 'public');
            $data['image'] = 'storage/' . $path;
        }

        $park = Park::find($this->editingPark['id']);
        if (!$park) {
            $park = Park::create($data);
        } else {
            $park->update($data);
        }



        // IndexNow Ping
        //app(IndexNowService::class)->ping(route('parks.show', $park->id));
        if ($park->status === 'active') {
            app(IndexNowService::class)->ping(route('parks.show', $park->slug));
        }

        session()->flash('success', 'Park gespeichert!');
        $this->resetEditingPark();
    }

    public function fetchGeodata()
    {
        $location = trim(($this->editingPark['name'] ?? '') . ', ' . ($this->editingPark['country'] ?? ''));
        $countryCode = [
            'Deutschland' => 'de',
            'Österreich' => 'at',
            'Schweiz' => 'ch',
            'France' => 'fr',
            'Italy' => 'it',
            'United Kingdom' => 'gb',
            'USA' => 'us',
        ][$this->editingPark['country']] ?? '';

        try {
            // 1. OSM Request mit Adressdetails
            $response = Http::withHeaders([
                'User-Agent' => 'FreizeitparkMap/1.0 (kontakt@deinedomain.de)',
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $location,
                'format' => 'json',
                'limit' => 1,
                'addressdetails' => 1,
                'countrycodes' => $countryCode,
            ]);

            $result = $response->json()[0] ?? null;

            if ($result && isset($result['address'])) {
                $address = $result['address'];

                $this->editingPark['latitude'] = $result['lat'];
                $this->editingPark['longitude'] = $result['lon'];
                $this->editingPark['street'] = $address['road'] ?? '';
                $this->editingPark['zip'] = $address['postcode'] ?? '';
                $this->editingPark['city'] = $address['city'] ?? $address['town'] ?? $address['village'] ?? '';
                $this->editingPark['country'] = $address['country'] ?? '';
                $this->editingPark['location'] = $address['country'] ?? '';

                // 2. Kontinent Mapping
                $continentMap = [
                    'Germany' => 'Europa',
                    'Deutschland' => 'Europa',
                    'France' => 'Europa',
                    'United Kingdom' => 'Europa',
                    'United States' => 'Nordamerika',
                    'Austria' => 'Europa',
                    'Switzerland' => 'Europa',
                    'Italy' => 'Europa',
                    'Japan' => 'Asien',
                    'Australia' => 'Australien',
                    'United Arab Emirates' => 'Asien',
                    'China' => 'Asien',
                    'Canada' => 'Nordamerika',
                    'Mexico' => 'Nordamerika',
                    'Brazil' => 'Südamerika',
                ];
                $this->editingPark['continent'] = $continentMap[$this->editingPark['country']] ?? '';

                // 3. Zeitzone per TimeZoneDB
                $tzApiKey = config('services.timezonedb.key');
                if ($tzApiKey && $this->editingPark['latitude'] && $this->editingPark['longitude']) {
                    $tzResponse = Http::get('http://api.timezonedb.com/v2.1/get-time-zone', [
                        'key' => $tzApiKey,
                        'format' => 'json',
                        'by' => 'position',
                        'lat' => $this->editingPark['latitude'],
                        'lng' => $this->editingPark['longitude'],
                    ]);

                    $tzData = $tzResponse->json();
                    if (!empty($tzData['zoneName'])) {
                        $this->editingPark['timezone'] = $tzData['zoneName']; // z. B. Europe/Berlin
                    }
                }

                session()->flash('success', 'Geodaten, Kontinent und Zeitzone erfolgreich abgerufen.');
            } else {
                session()->flash('error', 'Keine Geodaten gefunden.');
            }

        } catch (\Exception $e) {
            \Log::error('Fehler beim Abrufen der Geodaten', [
                'message' => $e->getMessage(),
            ]);
            session()->flash('error', 'Fehler beim Abrufen der Geodaten.');
        }
    }



    public function delete(Park $park)
    {
        if ($park->logo) {
            Storage::disk('public')->delete($park->logo);
        }
        $park->delete();
        session()->flash('success', 'Park gelöscht!');
    }

    public function closeModal() {
        $this->resetEditingPark();
    }

    public function toggleStatus($parkId)
    {
    $park = Park::find($parkId);

    if (!$park) {
        session()->flash('error', 'Park nicht gefunden.');
        return;
    }

    // Zyklisches Umschalten der Statuswerte
    $nextStatus = match ($park->status) {
        'pending' => 'active',
        'active' => 'inactive',
        'inactive' => 'revive',
        'revive' => 'pending',
        default => 'pending',
    };

    $park->status = $nextStatus;
    $park->save();

    session()->flash('success', "Status geändert zu: {$nextStatus}");

}

public function sortBy($field)
{
    if ($this->sortField === $field) {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }
}

}
