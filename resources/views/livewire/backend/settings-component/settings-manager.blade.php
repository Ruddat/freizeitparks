<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Seiteneinstellungen</h3>
                </div>

                <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $group === 'general' ? 'active' : '' }}" href="#general" wire:click.prevent="$set('group', 'general')">Allgemein</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $group === 'social' ? 'active' : '' }}" href="#social" wire:click.prevent="$set('group', 'social')">Social</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $group === 'seo' ? 'active' : '' }}" href="#seo" wire:click.prevent="$set('group', 'seo')">SEO</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $group === 'hero' ? 'active' : '' }}" href="#hero" wire:click.prevent="$set('group', 'hero')">Hero-Slider</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $group === 'maintenance' ? 'active' : '' }}" href="#maintenance" wire:click.prevent="$set('group', 'maintenance')">Wartungsmodus</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $group === 'footer' ? 'active' : '' }}" href="#footer" wire:click.prevent="$set('group', 'footer')">Footer</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $group === 'parks' ? 'active' : '' }}" href="#parks" wire:click.prevent="$set('group', 'parks')">
                            Freizeitparks
                        </a>
                    </li>
                </ul>

                <div class="card-body">
                    @if (session()->has('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if (session()->has('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="mb-4">
                        <label for="group" class="form-label">Einstellungsgruppe:</label>
                        <select wire:model.change="group" class="form-select w-auto" id="group">
                            <option value="general">Allgemein</option>
                            <option value="social">Social</option>
                            <option value="seo">SEO</option>
                            <option value="hero">Hero-Slider</option>
                            <option value="maintenance">Wartungsmodus</option>
                            <option value="footer">Footer</option>
                            <option value="parks">Freizeitparks</option>
                        </select>
                    </div>

                    <form wire:submit.prevent="save">
                        @csrf
                        @if ($group === 'hero')
                            <!-- Bestehender Hero-Slider Code bleibt unverändert -->
                            <div class="row">
                                @foreach ($heroSlides as $index => $image)
                                    <div class="col-md-12 mb-4 border-bottom pb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">Hero-Slide {{ $index }}</h5>
                                            <button type="button" class="btn btn-danger btn-sm" wire:click="deleteSlide({{ $index }})" onclick="return confirm('Sicher, dass du diesen Slider löschen möchtest?')">
                                                🗑️ Löschen
                                            </button>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="hero_{{ $index }}_image">Bild-URL</label>
                                            <input type="text" wire:model.lazy="settings.hero_{{ $index }}_image" class="form-control" id="hero_{{ $index }}_image">
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label" for="uploadHeroImage_{{ $index }}">Bild hochladen</label>
                                            <input type="file" wire:model="uploadHeroImage.{{ $index }}" class="form-control" id="uploadHeroImage_{{ $index }}">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="searchQuery_{{ $index }}">Bild suchen</label>
                                            <input type="text" wire:model.debounce.500ms="searchQueries.{{ $index }}" class="form-control" id="searchQuery_{{ $index }}" placeholder="z. B. günstiger Urlaub Mallorca">
                                        </div>

                                        <div class="mt-2">
                                            <button type="button" class="btn btn-outline-primary" wire:click="generateImageSuggestions({{ $index }})">
                                                🔍 Bilder suchen
                                            </button>
                                        </div>

                                        @if (!empty($imageSuggestions[$index]))
                                            <div class="row mt-3">
                                                @foreach ($imageSuggestions[$index] as $url)
                                                    <div class="col-6 col-md-3 mb-3">
                                                        <img
                                                            src="{{ $url }}"
                                                            class="img-fluid rounded border shadow-sm"
                                                            style="cursor: pointer;"
                                                            wire:click="selectSuggestedImage({{ $index }}, '{{ $url }}')"
                                                            title="Klicken zum Übernehmen"
                                                            alt="Vorschau für Hero-Slide {{ $index }}"
                                                        >
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="mb-3 mt-4">
                                            <label class="form-label" for="hero_{{ $index }}_headline">Headline</label>
                                            <input type="text" wire:model.lazy="settings.hero_{{ $index }}_headline" class="form-control" id="hero_{{ $index }}_headline">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="hero_{{ $index }}_subline">Subline</label>
                                            <input type="text" wire:model.lazy="settings.hero_{{ $index }}_subline" class="form-control" id="hero_{{ $index }}_subline">
                                        </div>
                                    </div>
                                @endforeach
                                <div class="mt-3">
                                    <button type="button" class="btn btn-success" wire:click="addNewSlide">
                                        + Neuer Hero-Slider
                                    </button>
                                </div>
                            </div>
                        @elseif ($group === 'maintenance')
                            <div class="row">
                                @foreach ($allSettings as $setting)
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-semibold" for="setting_{{ $setting->key }}">
                                            {{ $setting->description ?? ucfirst($setting->key) }}
                                        </label>

                                        @if ($setting->type === 'boolean')
                                            <div class="form-check form-switch mt-1">
                                                <input class="form-check-input" type="checkbox" wire:model="settings.{{ $setting->key }}" role="switch" id="switch_{{ $setting->key }}">
                                                <label class="form-check-label" for="switch_{{ $setting->key }}">
                                                    {{ $setting->description ?? ucfirst($setting->key) }} ({{ $settings[$setting->key] ? 'Aktiviert' : 'Deaktiviert' }})
                                                </label>
                                            </div>
                                        @elseif ($setting->key === 'maintenance_start_at' || $setting->key === 'maintenance_end_at')
                                            <input type="datetime-local" wire:model.lazy="settings.{{ $setting->key }}" class="form-control" id="setting_{{ $setting->key }}">
                                        @elseif ($setting->type === 'string')
                                            <input type="text" wire:model.lazy="settings.{{ $setting->key }}" class="form-control" id="setting_{{ $setting->key }}">
                                        @elseif ($setting->type === 'json')
                                            <textarea wire:model.lazy="settings.{{ $setting->key }}" class="form-control" rows="2" id="setting_{{ $setting->key }}"></textarea>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            @elseif ($group === 'parks')
                            <div class="row">
                                @foreach ($allSettings as $setting)
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-semibold" for="setting_{{ $setting->key }}">
                                            {{ $setting->description ?? ucfirst($setting->key) }}
                                        </label>

                                        @if ($setting->type === 'boolean')
                                            <div class="form-check form-switch mt-1">
                                                <input class="form-check-input" type="checkbox" wire:model="settings.{{ $setting->key }}" id="switch_{{ $setting->key }}">
                                                <label class="form-check-label" for="switch_{{ $setting->key }}">
                                                    {{ $settings[$setting->key] ? 'Aktiviert' : 'Deaktiviert' }}
                                                </label>
                                            </div>
                                        @else
                                            <input type="text" wire:model.lazy="settings.{{ $setting->key }}" class="form-control" id="setting_{{ $setting->key }}">
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            @elseif ($group === 'footer')
                            <div class="mb-4">
                                <label class="form-label">Footer-Texte</label>

                                @foreach ($settings['footer_texts'] ?? [] as $index => $text)
                                    <div class="input-group mb-2">
                                        <input type="text" wire:model.lazy="settings.footer_texts.{{ $index }}" class="form-control">
                                        <button type="button" class="btn btn-outline-danger" wire:click="removeFooterText({{ $index }})">🗑</button>
                                    </div>
                                @endforeach

                                <button type="button" class="btn btn-outline-primary" wire:click="addFooterText">
                                    ➕ Text hinzufügen
                                </button>
                            </div>


                            @elseif ($group === 'general')
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold" for="site_logo_upload">Logo hochladen</label>
                                    <input type="file" wire:model="uploadSettings.site_logo" class="form-control" id="site_logo_upload">

                                    @if (!empty($settings['site_logo']))
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="Logo" style="max-height: 80px;">
                                            <div class="text-muted mt-1"><small>{{ $settings['site_logo'] }}</small></div>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold" for="site_favicon_upload">Favicon hochladen</label>
                                    <input type="file" wire:model="uploadSettings.site_favicon" class="form-control" id="site_favicon_upload">

                                    @if (!empty($settings['site_favicon']))
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $settings['site_favicon']) }}" alt="Favicon" style="height: 32px; width: 32px;">
                                            <div class="text-muted mt-1"><small>{{ $settings['site_favicon'] }}</small></div>
                                        </div>
                                    @endif

                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" wire:model="useLogoAsFavicon" id="useLogoAsFavicon">
                                        <label class="form-check-label" for="useLogoAsFavicon">
                                            Logo automatisch als Favicon verwenden
                                        </label>
                                    </div>
                                </div>

                                @foreach ($allSettings as $setting)
                                    @if (!in_array($setting->key, ['site_logo', 'site_favicon']))
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label fw-semibold" for="setting_{{ $setting->key }}">
                                                {{ $setting->description ?? ucfirst($setting->key) }}
                                            </label>

                                            @if ($setting->type === 'string')
                                                <input type="text" wire:model.lazy="settings.{{ $setting->key }}" class="form-control" id="setting_{{ $setting->key }}">
                                            @elseif ($setting->type === 'boolean')
                                                <div class="form-check form-switch mt-1">
                                                    <input class="form-check-input" type="checkbox" wire:model="settings.{{ $setting->key }}" role="switch" id="switch_{{ $setting->key }}">
                                                    <label class="form-check-label" for="switch_{{ $setting->key }}">
                                                        {{ $setting->description ?? ucfirst($setting->key) }} ({{ $settings[$setting->key] ? 'Aktiviert' : 'Deaktiviert' }})
                                                    </label>
                                                </div>
                                            @elseif ($setting->type === 'json')
                                                <textarea wire:model.lazy="settings.{{ $setting->key }}" class="form-control" rows="2" id="setting_{{ $setting->key }}"></textarea>
                                            @elseif ($setting->type === 'file')
                                                <input type="text" wire:model.lazy="settings.{{ $setting->key }}" class="form-control mb-1" id="setting_{{ $setting->key }}">
                                                <small class="text-muted">Manueller Pfad – später auf Datei-Upload umstellbar</small>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>




                            @else
                            <div class="row">
                                @foreach ($allSettings as $setting)
                                    @if ($setting->group !== 'hero')
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label fw-semibold" for="setting_{{ $setting->key }}">
                                                {{ $setting->description ?? ucfirst($setting->key) }}
                                            </label>

                                            @if ($setting->type === 'string')
                                                <input type="text" wire:model.lazy="settings.{{ $setting->key }}" class="form-control" id="setting_{{ $setting->key }}">
                                            @elseif ($setting->type === 'boolean')
                                                <div class="form-check form-switch mt-1">
                                                    <input class="form-check-input" type="checkbox" wire:model="settings.{{ $setting->key }}" role="switch" id="switch_{{ $setting->key }}">
                                                    <label class="form-check-label" for="switch_{{ $setting->key }}">
                                                        {{ $setting->description ?? ucfirst($setting->key) }} ({{ $settings[$setting->key] ? 'Aktiviert' : 'Deaktiviert' }})
                                                    </label>
                                                </div>
                                            @elseif ($setting->type === 'json')
                                                <textarea wire:model.lazy="settings.{{ $setting->key }}" class="form-control" rows="2" id="setting_{{ $setting->key }}"></textarea>
                                            @elseif ($setting->type === 'file')
                                                <input type="text" wire:model.lazy="settings.{{ $setting->key }}" class="form-control mb-1" id="setting_{{ $setting->key }}">
                                                <small class="text-muted">Manueller Pfad – später auf Datei-Upload umstellbar</small>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <button type="submit" class="btn btn-primary">Einstellungen speichern</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
<style>
    .form-check-input:checked {
        background-color: #198754 !important;
        border-color: #198754 !important;
    }
</style>
@endpush
