<!-- livewire/park-suche.blade.php -->
<div class="mb-6 max-w-xl mx-auto relative">
    <div class="flex flex-col sm:flex-row gap-3 items-stretch">
        <!-- Suchfeld -->
        <input
            type="text"
            wire:model.live="suche"
            placeholder="🔍 Suche nach Freizeitpark, Ort oder Land..."
            class="flex-grow px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-700
                   placeholder-gray-400 focus:outline-none focus:ring focus:border-gray-500
                   transition duration-300 ease-in-out"
            x-ref="searchInput"
            @focus="showSuggestions = true"
            @blur="setTimeout(() => showSuggestions = false, 200)"
        />

        <!-- Standort-Button (schwarz) -->
        <button
            type="button"
            wire:click="useCurrentLocation"
            class="flex items-center justify-center gap-2 bg-black text-white px-4 py-3 rounded-lg
                   transition duration-300 ease-in-out transform hover:scale-105 hover:bg-gray-900 hover:shadow-md
                   cursor-pointer relative"
            :class="{ 'opacity-75': $wire.isLoading }"
            :disabled="$wire.isLoading"
        >
            @if($isLoading)
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 11c.552 0 1 .448 1 1s-.448 1-1 1-1-.448-1-1 .448-1 1-1zm9 1a9 9 0 11-18 0 9 9 0 0118 0zm-4.5 0a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                </svg>
            @endif
            In meiner Nähe
        </button>
    </div>

    <!-- Vorschau -->
    <div
        x-data="{ showSuggestions: false }"
        x-show="showSuggestions && $wire.suggestions.length"
        class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg max-h-60 overflow-y-auto"
    >
        <ul>
            @foreach($suggestions as $index => $suggestion)
            <li class="px-4 py-2 hover:bg-gray-100 text-gray-700">
                <a href="{{ route('parks.show', $suggestion['slug']) }}" class="block">
                    <strong>{{ $suggestion['name'] }}</strong>
                    <span class="text-sm text-gray-500">({{ $suggestion['location'] }}, {{ $suggestion['country'] }})</span>
                </a>
            </li>
            @endforeach
        </ul>
    </div>


    <script>
        document.addEventListener('alpine:init', () => {
            if (!Alpine.store('alerts')) {
                Alpine.store('alerts', {
                    alerts: [],
                    add(message, type = 'info') {
                        const id = Date.now();
                        this.alerts.push({ id, message, type });
                        setTimeout(() => {
                            this.alerts = this.alerts.filter(alert => alert.id !== id);
                        }, 5000);
                    }
                });
            }
        });
    </script>

<!-- Alert-Bereich -->
<div x-data x-show="$store.alerts && $store.alerts.alerts.length" class="mt-2 relative z-20" x-cloak>
    <div class="space-y-2">
        <template x-for="alert in $store.alerts.alerts" :key="alert.id">
            <div
                :class="{
                    'bg-blue-100 text-blue-700': alert.type === 'info',
                    'bg-green-100 text-green-700': alert.type === 'success',
                    'bg-red-100 text-red-700': alert.type === 'error'
                }"
                class="p-3 rounded-lg text-sm shadow-md"
                x-text="alert.message"
            ></div>
        </template>
    </div>
</div>



<script>
    document.addEventListener('alpine:init', () => {
        // Initialisiere den alerts-Store nur einmal
        Alpine.store('alerts', {
            alerts: [],
            add(message, type = 'info') {
                const id = Date.now().toString() + Math.random().toString(36).substring(2);
                this.alerts.push({ id, message, type });

                // Auto-Close nach 5 Sekunden
                setTimeout(() => {
                    this.alerts = this.alerts.filter(alert => alert.id !== id);
                }, 5000);
            }
        });
    });

    // Fallback, falls Alpine noch nicht geladen ist
    window.showAlert = function (message, type = 'info') {
        if (window.Alpine && Alpine.store('alerts')) {
            Alpine.store('alerts').add(message, type);
        } else {
            console.warn('Alpine.js noch nicht bereit. Fallback alert: ', message);
            alert(`${type.toUpperCase()}: ${message}`);
        }
    };

    document.addEventListener('livewire:init', () => {
        console.log('Livewire initialized');

        // Alerts über Livewire empfangen
        Livewire.on('alert', (data) => {
            showAlert(data.message, data.type);
        });
    // Scroll zu Parkliste nach Standortempfang
    Livewire.on('userLocationReceived', () => {
        setTimeout(() => {
            const el = document.getElementById('park-liste-anchor');
            if (el) {
                el.scrollIntoView({ behavior: 'smooth' });
            }
        }, 500);
    });
        // Standort-Anfrage
        window.addEventListener('get-user-location', () => {
            if (!navigator.geolocation) {
                Livewire.dispatch('alert', {
                    message: 'Geolocation wird von deinem Browser nicht unterstützt.',
                    type: 'error'
                });
                Livewire.dispatch('userLocationReceived', { lat: null, lng: null });
                return;
            }

            navigator.geolocation.getCurrentPosition(
                position => {
                    const coords = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    Livewire.dispatch('userLocationReceived', { coords });
                },
                error => {
                    Livewire.dispatch('alert', {
                        message: 'Standort konnte nicht abgefragt werden. Bitte erlaube die Standortfreigabe.',
                        type: 'error'
                    });
                    Livewire.dispatch('userLocationReceived', { lat: null, lng: null });
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });
    });
</script>

</div>
