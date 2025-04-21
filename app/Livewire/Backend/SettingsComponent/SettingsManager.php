<?php

namespace App\Livewire\Backend\SettingsComponent;

use Exception;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ModSiteSettings;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use App\Services\ImageDownloadService;
use Illuminate\Support\Facades\Storage;

class SettingsManager extends Component
{
    use WithFileUploads;

    public array $settings = [];
    public string $group = 'general';
    public array $imageSuggestions = [];
    public array $uploadHeroImage = [];
    public array $searchQueries = [];
    public array $heroSlides = [];
    public array $uploadSettings = [];
    public bool $useLogoAsFavicon = false;

    public function mount(): void
    {
        $this->loadSettings();
        $this->loadHeroSlides();
        $this->ensureMaintenanceSettingsExist();
        $this->ensureGeneralSettingsExist();
        $this->ensureParkSettingsExist();

    }


    private function ensureGeneralSettingsExist(): void
    {
        $generalDefaults = [
            [
                'key' => 'site_logo',
                'value' => '',
                'type' => 'file',
                'description' => 'Logo der Website',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'site_favicon',
                'value' => '',
                'type' => 'file',
                'description' => 'Favicon (32x32 PNG)',
                'group' => 'general',
                'is_public' => true,
            ],

            [
                'key' => 'site_icon_180',
                'value' => '',
                'type' => 'file',
                'description' => 'Apple Touch Icon (180x180)',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'site_icon_192',
                'value' => '',
                'type' => 'file',
                'description' => 'Android Icon (192x192)',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'site_icon_512',
                'value' => '',
                'type' => 'file',
                'description' => 'Manifest Icon (512x512)',
                'group' => 'general',
                'is_public' => true,
            ],
        ];

        foreach ($generalDefaults as $setting) {
            ModSiteSettings::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }



    private function ensureMaintenanceSettingsExist(): void
    {
        $maintenanceSettings = [
            [
                'key' => 'maintenance_mode', // Angepasst an MaintenanceService
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Wartungsmodus aktivieren',
                'group' => 'maintenance',
                'is_public' => false,
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'Die Seite befindet sich derzeit im Wartungsmodus. Wir sind bald wieder für dich da!',
                'type' => 'string',
                'description' => 'Wartungsmodus Nachricht',
                'group' => 'maintenance',
                'is_public' => true,
            ],
            [
                'key' => 'maintenance_start_at',
                'value' => '',
                'type' => 'string',
                'description' => 'Startzeit des Wartungsmodus (ISO 8601)',
                'group' => 'maintenance',
                'is_public' => false,
            ],
            [
                'key' => 'maintenance_end_at',
                'value' => '',
                'type' => 'string',
                'description' => 'Endzeit des Wartungsmodus (ISO 8601)',
                'group' => 'maintenance',
                'is_public' => false,
            ],
            [
                'key' => 'maintenance_allowed_ips',
                'value' => json_encode(['127.0.0.1']),
                'type' => 'json',
                'description' => 'Erlaubte IPs während des Wartungsmodus',
                'group' => 'maintenance',
                'is_public' => false,
            ],
        ];

        foreach ($maintenanceSettings as $setting) {
            $existing = ModSiteSettings::where('key', $setting['key'])->first();
            if (!$existing) {
                ModSiteSettings::create($setting);
            } else {
                // Aktualisiere nur Gruppe und Beschreibung, falls sie abweichen
                $existing->update([
                    'group' => $setting['group'],
                    'type' => $setting['type'],
                    'description' => $setting['description'],
                    'is_public' => $setting['is_public'],
                ]);
            }
        }

        // Entferne den alten maintenance_mode_active-Eintrag, falls er existiert
        $oldMaintenance = ModSiteSettings::where('key', 'maintenance_mode_active')->first();
        if ($oldMaintenance) {
            $oldMaintenance->delete();
        }
    }

    private function ensureParkSettingsExist(): void
    {
        $defaults = [
            [
                'key' => 'default_map_lat',
                'value' => '51.1657',
                'type' => 'string',
                'description' => 'Standardbreitengrad der Karte',
                'group' => 'parks',
                'is_public' => true,
            ],
            [
                'key' => 'default_map_lng',
                'value' => '10.4515',
                'type' => 'string',
                'description' => 'Standardlängengrad der Karte',
                'group' => 'parks',
                'is_public' => true,
            ],
            [
                'key' => 'default_map_zoom',
                'value' => '6',
                'type' => 'string',
                'description' => 'Standard-Zoomlevel der Karte',
                'group' => 'parks',
                'is_public' => true,
            ],
            [
                'key' => 'enable_radius_filter',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Radius-Filter in Parksuche aktivieren',
                'group' => 'parks',
                'is_public' => true,
            ],
        ];

        foreach ($defaults as $setting) {
            ModSiteSettings::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }

    public function updatedUploadSettings($file, string $key): void
    {
        try {
            $convertedPath = $this->saveAndConvertSettingImage($file, $key, $key === 'site_favicon' ? 'favicon' : 'logo');

            if ($convertedPath) {
                $this->settings[$key] = $convertedPath;

                ModSiteSettings::updateOrCreate(['key' => $key], [
                    'value' => $convertedPath,
                ]);

                // Automatisch Favicon erzeugen?
                if ($key === 'site_logo' && $this->useLogoAsFavicon) {
                    $faviconPath = $this->saveAndConvertSettingImage($file, 'favicon', 'favicon');

                    if ($faviconPath) {
                        $this->settings['site_favicon'] = $faviconPath;
                        ModSiteSettings::updateOrCreate(['key' => 'site_favicon'], [
                            'value' => $faviconPath,
                        ]);
                    }
                }

                session()->flash('success', ucfirst($key) . ' erfolgreich hochgeladen und konvertiert.');
            } else {
                session()->flash('error', 'Konvertierung fehlgeschlagen.');
            }
        } catch (Exception $e) {
            session()->flash('error', 'Fehler beim Upload von ' . $key . ': ' . $e->getMessage());
        }
    }


    protected array $settingImageSizes = [
        'logo' => [40, 40],       // max 300px Breite
        'favicon' => [32, 32],       // exakt 32x32
        'site_icon_180' => [180, 180],  // Apple Touch Icon
        'site_icon_192' => [192, 192],  // Android Icon
        'site_icon_512' => [512, 512],  // Manifest Icon
    ];

    public function saveAndConvertSettingImage(UploadedFile $file, string $name, string $type = 'logo'): ?string
    {
        try {
            $extension = 'webp';

            //$manager = new ImageManager(new Driver());
            try {
                $manager = new ImageManager(new \Intervention\Image\Drivers\Imagick\Driver());
            } catch (\Exception $e) {
                \Log::warning("Imagick not available, falling back to GD: " . $e->getMessage());
                $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            }

            $image = $manager->read($file->getRealPath());

            // Wenn Hauptlogo: gleich alle zusätzlichen Icons erzeugen
            if ($type === 'logo') {
                foreach (['site_icon_180', 'site_icon_192', 'site_icon_512'] as $iconKey) {
                    [$w, $h] = $this->settingImageSizes[$iconKey];
                    $resized = clone $image;
                    $resized->resize($w, $h);
                    $iconPath = 'site-assets/' . $iconKey . '-' . uniqid() . '.' . $extension;

                    Storage::disk('public')->put($iconPath, (string) $resized->toWebp(quality: 85));

                    // Direkt speichern in DB und Component-Array
                    $this->settings[$iconKey] = $iconPath;
                    ModSiteSettings::updateOrCreate(['key' => $iconKey], [
                        'value' => $iconPath,
                    ]);
                }
            }

            // Normales Bild für aktuelle Einstellung
            [$width, $height] = $this->settingImageSizes[$type] ?? [null, null];
            if ($type === 'logo') {
                $image->resize($width, $height, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });
            } elseif ($width && $height) {
                $image->resize($width, $height);
            }

            $filename = 'site-assets/' . $name . '-' . uniqid() . '.' . $extension;
            Storage::disk('public')->put($filename, (string) $image->toWebp(quality: 85));

            return $filename;
        } catch (\Exception $e) {
            logger()->error('Image conversion failed: ' . $e->getMessage());
            return null;
        }
    }




    public function updatedSettings($value, string $key): void
    {
        $setting = ModSiteSettings::where('key', $key)->first();
        if (!$setting) {
            session()->flash('error', "Einstellung mit Schlüssel '{$key}' nicht gefunden.");
            return;
        }

        if ($setting->type === 'json') {
            $setting->value = json_encode($value);
        } elseif ($setting->type === 'boolean') {
            $setting->value = $value ? '1' : '0';
        } else {
            $setting->value = $value;
        }
        $setting->save();

        $this->loadSettings();
        $this->loadHeroSlides();
    }

    public function loadSettings(): void
    {
        $this->settings = ModSiteSettings::where('group', $this->group)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->type === 'json' ? json_decode($setting->value, true) : $setting->value];
            })
            ->toArray();
    }

    public function loadHeroSlides(): void
    {
        $this->heroSlides = ModSiteSettings::where('group', 'hero')
            ->where('key', 'like', 'hero_%_image')
            ->get()
            ->mapWithKeys(function ($setting) {
                return [preg_replace('/^hero_(\d+)_image$/', '$1', $setting->key) => $setting->value];
            })
            ->toArray();
    }

    public function updatedGroup(): void
    {
        $this->loadSettings();
        $this->loadHeroSlides();
    }

    public function generateImageSuggestions(int $index): void
    {
        try {
            $query = $this->searchQueries[$index] ?? 'günstiger Urlaub';
            $service = new ImageDownloadService();
            $this->imageSuggestions[$index] = $service->searchImages($query, '', 6);
        } catch (\Exception $e) {
            session()->flash('error', 'Fehler beim Abrufen der Bildvorschläge: ' . $e->getMessage());
        }
    }

    public function selectSuggestedImage(int $index, string $url): void
    {
        try {
            $name = $this->settings["hero_{$index}_headline"] ?? 'hero';
            $service = new ImageDownloadService();
            $pfad = $service->saveImageFromUrl($url, $name);

            if ($pfad) {
                $this->settings["hero_{$index}_image"] = $pfad;
                $setting = ModSiteSettings::where('key', "hero_{$index}_image")->first();
                if ($setting) {
                    $setting->value = $pfad;
                    $setting->save();
                }
                unset($this->imageSuggestions[$index]);
                session()->flash('success', 'Bild erfolgreich übernommen.');
                $this->loadHeroSlides();
            } else {
                session()->flash('error', 'Bild konnte nicht gespeichert werden.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Fehler beim Speichern des Bildes: ' . $e->getMessage());
        }
    }

    public function updatedUploadHeroImage($file, string $key): void
    {
        try {
            $index = str_replace('uploadHeroImage.', '', $key);
            $filename = 'hero-slides/slide-' . $index . '-' . uniqid() . '.jpg';

            $path = $file->storeAs('public/' . dirname($filename), basename($filename));
            $this->settings["hero_{$index}_image"] = str_replace('public/', '', $path);

            $setting = ModSiteSettings::where('key', "hero_{$index}_image")->first();
            if ($setting) {
                $setting->value = str_replace('public/', '', $path);
                $setting->save();
            }

            session()->flash('success', 'Bild erfolgreich hochgeladen.');
            $this->loadHeroSlides();
        } catch (\Exception $e) {
            session()->flash('error', 'Fehler beim Hochladen des Bildes: ' . $e->getMessage());
        }
    }

    public function addNewSlide(): void
    {
        try {
            $existingSlides = ModSiteSettings::where('group', 'hero')
                ->where('key', 'like', 'hero_%_image')
                ->count();
            $newIndex = $existingSlides + 1;

            ModSiteSettings::insert([
                [
                    'key' => "hero_{$newIndex}_image",
                    'value' => '',
                    'type' => 'file',
                    'description' => "Hero-Slider {$newIndex}: Bild",
                    'group' => 'hero',
                    'is_public' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => "hero_{$newIndex}_headline",
                    'value' => "Schnäppchen {$newIndex}",
                    'type' => 'string',
                    'description' => "Hero-Slider {$newIndex}: Headline",
                    'group' => 'hero',
                    'is_public' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => "hero_{$newIndex}_subline",
                    'value' => "Günstig Reisen {$newIndex}",
                    'type' => 'string',
                    'description' => "Hero-Slider {$newIndex}: Subline",
                    'group' => 'hero',
                    'is_public' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            $this->loadSettings();
            $this->loadHeroSlides();
            session()->flash('success', 'Neuer Hero-Slider hinzugefügt.');
        } catch (\Exception $e) {
            session()->flash('error', 'Fehler beim Hinzufügen des Sliders: ' . $e->getMessage());
        }
    }

    public function deleteSlide(int $index): void
    {
        try {
            ModSiteSettings::whereIn('key', [
                "hero_{$index}_image",
                "hero_{$index}_headline",
                "hero_{$index}_subline"
            ])->delete();

            $slides = ModSiteSettings::where('group', 'hero')
                ->where('key', 'like', 'hero_%_image')
                ->orderBy('key')
                ->get();

            $newIndex = 1;
            foreach ($slides as $slide) {
                $oldIndex = preg_replace('/^hero_(\d+)_image$/', '$1', $slide->key);
                if ($oldIndex != $newIndex) {
                    ModSiteSettings::where('key', "hero_{$oldIndex}_image")
                        ->update(['key' => "hero_{$newIndex}_image"]);
                    ModSiteSettings::where('key', "hero_{$oldIndex}_headline")
                        ->update(['key' => "hero_{$newIndex}_headline", 'description' => "Hero-Slider {$newIndex}: Headline"]);
                    ModSiteSettings::where('key', "hero_{$oldIndex}_subline")
                        ->update(['key' => "hero_{$newIndex}_subline", 'description' => "Hero-Slider {$newIndex}: Subline"]);
                }
                $newIndex++;
            }

            $this->loadSettings();
            $this->loadHeroSlides();
            session()->flash('success', "Hero-Slider {$index} wurde gelöscht.");
        } catch (\Exception $e) {
            session()->flash('error', 'Fehler beim Löschen des Sliders: ' . $e->getMessage());
        }
    }

    public function save(): void
    {
        session()->flash('success', 'Einstellungen wurden gespeichert.');
    }

    public function addFooterText(): void
    {
        $key = 'footer_texts';
        $setting = ModSiteSettings::where('key', $key)->first();

        // Wenn das Setting noch nicht existiert, neu anlegen
        if (!$setting) {
            $setting = ModSiteSettings::create([
                'key' => $key,
                'value' => json_encode(['Neuer Footer-Text']),
                'type' => 'json',
                'description' => 'Footer-Texte für wechselnde Anzeige',
                'group' => 'footer',
                'is_public' => true,
            ]);
        } else {
            $texts = json_decode($setting->value, true) ?? [];
            $texts[] = 'Neuer Footer-Text';
            $setting->value = json_encode($texts);
            $setting->save();
        }

        // Sync mit Component-Array
        $this->settings[$key] = json_decode($setting->value, true);
    }

    public function removeFooterText(int $index): void
    {
        $key = 'footer_texts';
        $setting = ModSiteSettings::where('key', $key)->first();

        if (!$setting) return;

        $texts = json_decode($setting->value, true) ?? [];

        unset($texts[$index]);
        $texts = array_values($texts); // Reindexieren

        $setting->value = json_encode($texts);
        $setting->save();

        $this->settings[$key] = $texts;
    }


    public function render()
    {
        $allSettings = ModSiteSettings::where('group', $this->group)->get();
        return view('livewire.backend.settings-component.settings-manager', [
            'allSettings' => $allSettings,
        ])->layout('backend.layouts.backend');
    }
}
