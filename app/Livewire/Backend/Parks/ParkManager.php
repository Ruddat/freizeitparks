<?php

namespace App\Livewire\Backend\Parks;

use App\Models\Park;
use App\Models\ParkOpeningHour;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ParkManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $editingPark = null;
    public $isModalOpen = false;
    public $perPage = 10;
    public $logoUpload;
    public $defaultOpen;
    public $defaultClose;
    public $applyToAll = false;
    public $keywords = ''; // Neues Feld für Stichworte
    public $opening_hours = [
        'monday' => ['open' => '', 'close' => ''],
        'tuesday' => ['open' => '', 'close' => ''],
        'wednesday' => ['open' => '', 'close' => ''],
        'thursday' => ['open' => '', 'close' => ''],
        'friday' => ['open' => '', 'close' => ''],
        'saturday' => ['open' => '', 'close' => ''],
        'sunday' => ['open' => '', 'close' => ''],
    ];

    public $imageUpload;
    public $imageUploadPath = 'images/parks/';

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
        ];
        $this->opening_hours = [
            'monday' => ['open' => '', 'close' => ''],
            'tuesday' => ['open' => '', 'close' => ''],
            'wednesday' => ['open' => '', 'close' => ''],
            'thursday' => ['open' => '', 'close' => ''],
            'friday' => ['open' => '', 'close' => ''],
            'saturday' => ['open' => '', 'close' => ''],
            'sunday' => ['open' => '', 'close' => ''],
        ];
        $this->isModalOpen = false;
        $this->logoUpload = null;
        $this->imageUpload = null;
        $this->defaultOpen = '';
        $this->defaultClose = '';
        $this->applyToAll = false;
        $this->keywords = '';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatingLogoUpload()
    {
        if ($this->isModalOpen) {
            $this->dispatch('open-modal');
        }
    }

    public function updatingApplyToAll()
    {
        $this->updateOpeningHours();
    }



    public function generateDescription()
    {
        $this->validate([
            'editingPark.name' => 'required|string|max:255',
            'keywords' => 'nullable|string|max:500',
        ]);

        $parkName = $this->editingPark['name'];
        $keywords = $this->keywords ?: '';
        $prompt = "Erstelle eine detaillierte und ansprechende Beschreibung für den Freizeitpark '$parkName'. Berücksichtige die folgenden Stichworte, falls angegeben: $keywords. Beschreibe Attraktionen, Atmosphäre, Zielgruppe (z. B. Familien, Abenteuerlustige), besondere Merkmale (z. B. Themenbereiche, Shows) und praktische Informationen (z. B. Restaurants, Zugänglichkeit). Die Beschreibung soll maximal 3000 Wörter lang sein, informativ, freundlich und für Besucher attraktiv sein. Verwende HTML-Formatierung (z. B. <h3>, <p>, <ul>) für Struktur.";

        try {
            // Generierungsanfrage
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . config('services.deepinfra.token'),
                'Content-Type' => 'application/json',
            ])->post('https://api.deepinfra.com/v1/inference/mistralai/Mixtral-8x7B-Instruct-v0.1', [
                'input' => "[INST] {$prompt} [/INST]",
                'max_new_tokens' => 4000,
                'temperature' => 0.7,
                'top_p' => 0.9,
            ]);

            if (!$response->successful()) {
                \Log::error('DeepInfra Generierung fehlgeschlagen', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                session()->flash('error', 'Fehler bei der Generierung der Beschreibung: API-Anfrage fehlgeschlagen.');
                return;
            }

            $generatedText = $response->json()['results'][0]['generated_text'] ?? '';
            if (empty($generatedText)) {
                \Log::error('DeepInfra Generierung: Kein Text in der Antwort', [
                    'response' => $response->json(),
                ]);
                session()->flash('error', 'Fehler bei der Generierung: Kein Text empfangen.');
                return;
            }

            // Wortanzahl prüfen
            $wordCount = str_word_count(strip_tags($generatedText));
            if ($wordCount > 3000) {
                $generatedText = Str::words($generatedText, 3000, '');
                \Log::info('Beschreibung gekürzt auf 3000 Wörter', ['original_words' => $wordCount]);
            }

            // Moderation der generierten Beschreibung
            $moderationPrompt = "Analysiere diesen Text auf Hassrede, Gewalt, Diskriminierung oder unangemessene Sprache. Antworte ausschließlich mit 'OK' wenn der Text unbedenklich ist, sonst mit 'BAD'. Text: \"{$generatedText}\"";
            $moderationResponse = Http::timeout(15)->withHeaders([
                'Authorization' => 'Bearer ' . config('services.deepinfra.token'),
                'Content-Type' => 'application/json',
            ])->post('https://api.deepinfra.com/v1/inference/mistralai/Mixtral-8x7B-Instruct-v0.1', [
                'input' => "[INST] {$moderationPrompt} [/INST]",
                'max_new_tokens' => 50,
                'temperature' => 0.3,
                'top_p' => 0.9,
            ]);

            if (!$moderationResponse->successful()) {
                \Log::error('DeepInfra Moderation fehlgeschlagen', [
                    'status' => $moderationResponse->status(),
                    'body' => $moderationResponse->body(),
                ]);
                // Fallback: Beschreibung laden, aber mit Warnung
                $this->editingPark['description'] = $generatedText;
                $this->dispatch('updateJoditEditor', $generatedText);
                session()->flash('warning', 'Moderation fehlgeschlagen. Bitte prüfen Sie die Beschreibung manuell.');
                return;
            }

            $moderationOutput = $moderationResponse->json()['results'][0]['generated_text'] ?? '';
            \Log::info('Moderationsantwort', ['output' => $moderationOutput]); // Debugging

            if (trim($moderationOutput) !== 'OK') {
                \Log::warning('Moderation: Unangemessener Inhalt erkannt', [
                    'moderation_output' => $moderationOutput,
                    'generated_text' => substr($generatedText, 0, 500),
                ]);
                // Fallback: Beschreibung laden, aber mit Warnung
                $this->editingPark['description'] = $generatedText;
                $this->dispatch('updateJoditEditor', $generatedText);
                session()->flash('warning', 'Moderation hat mögliche Probleme erkannt. Bitte prüfen Sie die Beschreibung manuell.');
                return;
            }

            // Beschreibung in den Editor laden
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
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.backend.parks.park-manager', [
            'parks' => $parks,
            'parkTypes' => ['Freizeitpark', 'Wasserpark', 'Zoo', 'Themenpark', 'Andere'],
        ])
        ->extends('backend.layouts.backend')
        ->section('main-content');
    }

    public function edit($id)
    {
        $park = Park::with('openingHours')->find($id);
        if ($park) {
            $this->editingPark = [
                'id' => $park->id,
                'external_id' => $park->external_id,
                'queue_times_id' => $park->queue_times_id,
                'group_id' => $park->group_id,
                'name' => $park->name,
                'group_name' => $park->group_name,
                'location' => $park->location,
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
                'created_at' => $park->created_at ? $park->created_at->toDateTimeString() : null,
                'updated_at' => $park->updated_at ? $park->updated_at->toDateTimeString() : null,
            ];

            // Öffnungszeiten laden
            $this->opening_hours = [
                'monday' => ['open' => '', 'close' => ''],
                'tuesday' => ['open' => '', 'close' => ''],
                'wednesday' => ['open' => '', 'close' => ''],
                'thursday' => ['open' => '', 'close' => ''],
                'friday' => ['open' => '', 'close' => ''],
                'saturday' => ['open' => '', 'close' => ''],
                'sunday' => ['open' => '', 'close' => ''],
            ];
            \Log::info('Park Opening Hours', ['opening_hours' => $park->openingHours->toArray()]);
            foreach ($park->openingHours as $hour) {
                $this->opening_hours[$hour->day] = [
                    'open' => $hour->formatted_open, // Verwendet den Accessor
                    'close' => $hour->formatted_close, // Verwendet den Accessor
                ];
            }
            \Log::info('Updated opening_hours', ['opening_hours' => $this->opening_hours]);

            // Prüfen, ob alle nicht-leeren Tage die gleichen Öffnungszeiten haben
            $nonEmptyDays = array_filter($this->opening_hours, function ($day) {
                return !empty($day['open']) && !empty($day['close']);
            });

            if (!empty($nonEmptyDays)) {
                $firstDay = reset($nonEmptyDays);
                $allSame = array_reduce(
                    array_keys($nonEmptyDays),
                    function ($carry, $day) use ($firstDay) {
                        $isSame = $carry && $this->opening_hours[$day]['open'] === $firstDay['open'] && $this->opening_hours[$day]['close'] === $firstDay['close'];
                        \Log::info('Comparing day', [
                            'day' => $day,
                            'open' => $this->opening_hours[$day]['open'],
                            'close' => $this->opening_hours[$day]['close'],
                            'first_open' => $firstDay['open'],
                            'first_close' => $firstDay['close'],
                            'is_same' => $isSame,
                        ]);
                        return $isSame;
                    },
                    true
                );

                if ($allSame) {
                    $this->defaultOpen = $firstDay['open'];
                    $this->defaultClose = $firstDay['close'];
                    $this->applyToAll = true;
                    \Log::info('All non-empty days same, setting defaults', [
                        'defaultOpen' => $this->defaultOpen,
                        'defaultClose' => $this->defaultClose,
                        'applyToAll' => $this->applyToAll,
                    ]);
                } else {
                    $this->defaultOpen = '';
                    $this->defaultClose = '';
                    $this->applyToAll = false;
                    \Log::info('Non-empty days differ, resetting defaults', [
                        'defaultOpen' => $this->defaultOpen,
                        'defaultClose' => $this->defaultClose,
                        'applyToAll' => $this->applyToAll,
                    ]);
                }
            } else {
                $this->defaultOpen = '';
                $this->defaultClose = '';
                $this->applyToAll = false;
                \Log::info('No non-empty days, resetting defaults', [
                    'defaultOpen' => $this->defaultOpen,
                    'defaultClose' => $this->defaultClose,
                    'applyToAll' => $this->applyToAll,
                ]);
            }

            $this->isModalOpen = true;
            $this->dispatch('open-modal');
        } else {
            session()->flash('error', 'Park nicht gefunden.');
        }
    }


    public function create()
    {
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
            'opening_hours.*.open' => 'nullable|date_format:H:i',
            'opening_hours.*.close' => 'nullable|date_format:H:i',
            'defaultOpen' => 'nullable|date_format:H:i',
            'defaultClose' => 'nullable|date_format:H:i',
        ]);

        // 🧱 Daten vorbereiten
        $data = [
            'external_id' => $this->editingPark['external_id'] ?: null, // Leer -> NULL
            'queue_times_id' => $this->editingPark['queue_times_id'],
            'group_id' => $this->editingPark['group_id'],
            'name' => $this->editingPark['name'],
            'group_name' => $this->editingPark['group_name'],
            'location' => $this->editingPark['location'],
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

        // 🏞 Logo hochladen
        if ($this->logoUpload) {
            if (!empty($this->editingPark['logo'])) {
                Storage::disk('public')->delete(str_replace('storage/', '', $this->editingPark['logo']));
            }
            $path = $this->logoUpload->store('logos', 'public');
            $data['logo'] = 'storage/' . $path;
        }

        // 🖼 Bild hochladen
        if ($this->imageUpload) {
            $directory = 'parks';
            Storage::disk('public')->makeDirectory($directory);
            if (!empty($this->editingPark['image'])) {
                Storage::disk('public')->delete(str_replace('storage/', '', $this->editingPark['image']));
            }
            $path = $this->imageUpload->store('parks', 'public');
            $data['image'] = 'storage/' . $path;
            \Log::info('Bild hochgeladen', ['path' => $path, 'image' => $data['image']]);
        }

        // ✅ Park speichern (neu oder bestehend)
        $park = Park::find($this->editingPark['id']);
        if (!$park) {
            $park = Park::create($data);
        } else {
            $park->update($data);
        }

        // 🕒 Öffnungszeiten aktualisieren
        $park->openingHours()->delete();
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        if ($this->applyToAll && $this->defaultOpen && $this->defaultClose) {
            foreach ($days as $day) {
                $park->openingHours()->create([
                    'day' => $day,
                    'open' => $this->defaultOpen . ':00',
                    'close' => $this->defaultClose . ':00',
                ]);
            }
            \Log::info('Saved opening hours (applyToAll)', [
                'park_id' => $park->id,
                'defaultOpen' => $this->defaultOpen,
                'defaultClose' => $this->defaultClose,
            ]);
        } else {
            foreach ($this->opening_hours as $day => $times) {
                if ($times['open'] || $times['close']) {
                    $park->openingHours()->create([
                        'day' => $day,
                        'open' => $times['open'] ? $times['open'] . ':00' : null,
                        'close' => $times['close'] ? $times['close'] . ':00' : null,
                    ]);
                }
            }
            \Log::info('Saved opening hours (individual)', [
                'park_id' => $park->id,
                'opening_hours' => $this->opening_hours,
            ]);
        }

        session()->flash('success', 'Park gespeichert!');
        $this->resetEditingPark();
    }


    public function fetchGeodata()
    {
        $name = $this->editingPark['name'] ?? '';
        $country = $this->editingPark['country'] ?? '';
        $location = trim($name . ', ' . $country);

        // Optionale Country-Code-Mapping
        $countryCodes = [
            'Deutschland' => 'de',
            'Österreich' => 'at',
            'Schweiz' => 'ch',
            'France' => 'fr',
            'Italy' => 'it',
        ];
        $countryCode = $countryCodes[$country] ?? '';

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'FreizeitparkMap/1.0 (kontakt@deinedomain.de)',
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $location,
                'format' => 'json',
                'limit' => 1,
                'countrycodes' => $countryCode,
            ]);
//dd($response->json());
            $result = $response->json()[0] ?? null;

            if ($result) {
                $this->editingPark['latitude'] = $result['lat'];
                $this->editingPark['longitude'] = $result['lon'];
                session()->flash('success', 'Geodaten erfolgreich abgerufen für: ' . $location);
            } else {
                session()->flash('error', 'Keine Geodaten gefunden für: ' . $location);
            }
        } catch (\Exception $e) {
            \Log::error('Fehler beim Abrufen der Geodaten', [
                'message' => $e->getMessage(),
                'location' => $location
            ]);
            session()->flash('error', 'Fehler beim Abrufen der Geodaten.');
        }
    }

    public function updatingDefaultOpen()
    {
        $this->updateOpeningHours();
    }

    public function updatingDefaultClose()
    {
        $this->updateOpeningHours();
    }

    protected function updateOpeningHours()
    {
        if ($this->applyToAll && $this->defaultOpen && $this->defaultClose) {
            foreach (array_keys($this->opening_hours) as $day) {
                $this->opening_hours[$day]['open'] = $this->defaultOpen;
                $this->opening_hours[$day]['close'] = $this->defaultClose;
            }
            // Optional: Dispatch ein Event, um das Frontend zu aktualisieren
            $this->dispatch('update-opening-hours');
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

    public function closeModal()
    {
        $this->resetEditingPark();
    }
}
