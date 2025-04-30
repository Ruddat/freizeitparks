<div class="container mx-auto px-4 bg-[#010b3f] text-white">
    <!-- Suchfeld -->
    <div class="max-w-7xl mx-auto px-6 lg:px-12 py-8 mb-6">
        <h2 class="text-3xl font-bold mb-4 text-center">Finde deinen perfekten Freizeitpark! 🎢</h2>
        <input
            type="search"
            wire:model.live.debounce.500ms="suche"
            placeholder="Suche nach Freizeitpark, Ort oder Land..."
            class="w-full px-5 py-3 bg-[#010b3f] border border-gray-600 rounded-xl
                   focus:outline-none focus:ring-2 focus:ring-yellow-400
                   text-white placeholder-gray-400 shadow-sm"
        />
    </div>


<!-- Aktive Filter-Chips -->
@if($suche || $land || $status !== 'alle')
    <div class="hidden md:block max-w-6xl mx-auto px-4 flex flex-wrap items-center gap-2 mb-4 animate-slideIn">
        @if($suche)
            <span class="bg-indigo-600/10 text-indigo-100 px-2.5 py-1 text-xs rounded-full flex items-center border border-indigo-500/20 shadow-xs">
                Suche: {{ $suche }}
                <button wire:click="$set('suche', '')" class="ml-1.5 text-indigo-300 hover:text-indigo-100 transition-colors text-xs">×</button>
            </span>
        @endif

        @if($land)
            <span class="bg-indigo-600/10 text-indigo-100 px-2.5 py-1 text-xs rounded-full flex items-center border border-indigo-500/20 shadow-xs">
                Land: {{ $land }}
                <button wire:click="$set('land', '')" class="ml-1.5 text-indigo-300 hover:text-indigo-100 transition-colors text-xs">×</button>
            </span>
        @endif

        @if($status !== 'alle')
            <span class="bg-emerald-600/10 text-emerald-100 px-2.5 py-1 text-xs rounded-full flex items-center border border-emerald-500/20 shadow-xs">
                Status: {{ match($status) {
                    'open' => 'Geöffnet',
                    'closed' => 'Geschlossen',
                    'unknown' => 'Unbekannt',
                } }}
                <button wire:click="$set('status', 'alle')" class="ml-1.5 text-emerald-300 hover:text-emerald-100 transition-colors text-xs">×</button>
            </span>
        @endif

        <button wire:click="resetFilter"
                class="ml-2 px-2.5 py-1 text-xs bg-indigo-600/10 text-indigo-100 rounded-full font-medium transition-colors hover:bg-indigo-600/20 border border-indigo-500/20 shadow-xs">
            Alle zurücksetzen
        </button>
    </div>
@endif

<!-- Länder-Filter -->
<div class="hidden md:block max-w-6xl mx-auto px-6 mb-6">
    <div class="flex items-center gap-3 mb-3">
        <span class="text-sm font-semibold text-white">Land:</span>
        <button wire:click="$set('land', '')"
                class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200
                       {{ $land === '' ? 'bg-indigo-600 text-white shadow-md' : 'bg-indigo-800/30 text-indigo-200 hover:bg-indigo-800/50 hover:shadow-sm border border-indigo-500/30' }}">
            Alle
        </button>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2">
        @foreach($laender as $landOption)
            <button wire:click="$set('land', '{{ $landOption }}')"
                    class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200
                           {{ $land === $landOption ? 'bg-indigo-600 text-white shadow-md' : 'bg-indigo-800/30 text-indigo-200 hover:bg-indigo-800/50 hover:shadow-sm border border-indigo-500/30' }}">
                {{ $landOption }}
            </button>
        @endforeach
    </div>
</div>

<!-- Status-Filter -->
<!-- Status-Filter -->
<div class="hidden md:block max-w-6xl mx-auto px-6 mb-6">
    <div class="flex items-center gap-3 mb-3">
        <span class="text-sm font-semibold text-white">Status:</span>
        @foreach([
            'alle' => 'Alle',
            'open' => '🟢 Geöffnet',
            'closed' => '🔴 Geschlossen',
            'unknown' => '⚪ Unbekannt'
        ] as $key => $label)
            <button wire:click="$set('status', @js($key))"
                    class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200
                           {{ $status === $key ? 'bg-emerald-600 text-white shadow-md' : 'bg-emerald-800/30 text-emerald-200 hover:bg-emerald-800/50 hover:shadow-sm border border-emerald-500/30' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>
</div>

    <!-- Mobile-Filter -->
    <div class="md:hidden grid grid-cols-1 gap-6 mb-8">
        <!-- Bestehende Mobile-Filter -->
        <div>
            <label for="land-mobile" class="block text-sm font-semibold text-gray-700 mb-2">Land</label>
            <select id="land-mobile" wire:model.live="land" class="w-full border border-gray-200 p-3 rounded-xl bg-white text-gray-900 shadow-sm">
                <option value="">Alle Länder</option>
                @foreach($laender as $landOption)
                    <option value="{{ $landOption }}">{{ $landOption }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="status-mobile" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
            <select id="status-mobile" wire:model.live="status" class="w-full border border-gray-200 p-3 rounded-xl bg-white text-gray-900 shadow-sm">
                <option value="alle">Alle</option>
                <option value="open">🟢 Geöffnet</option>
                <option value="closed">🔴 Geschlossen</option>
                <option value="unknown">⚪ Unbekannt</option>
            </select>
        </div>
        <!-- Neuer Reset-Button -->
        <div>
            <button wire:click="resetFilter" class="w-full bg-indigo-600 text-white font-semibold py-2 rounded-xl hover:bg-indigo-700 transition-colors">
                Alle Filter zurücksetzen
            </button>
        </div>
    </div>

    <style>
.flip-container {
    perspective: 1000px;
    position: relative;
    min-height: 28rem;
    cursor: pointer;
}
.flipper {
    position: relative;
    width: 100%;
    height: 100%;
    transition: transform 0.8s cubic-bezier(0.4, 0.2, 0.2, 1);
    transform-style: preserve-3d;
}
.flip-container.flipped .flipper {
    transform: rotateY(180deg);
}
.front, .back {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    backface-visibility: hidden; /* Wichtig für beide Seiten */
}
.front {
    transform: rotateY(0deg); /* Explizit auf 0 Grad setzen */
    z-index: 2;
}
.back {
    transform: rotateY(180deg); /* Rückseite um 180 Grad gedreht */
    background-color: #1f2937;
    padding: 1.5rem;
    overflow-y: auto;
}
    </style>

<section id="park-liste-anchor" class="max-w-7xl mx-auto px-6 lg:px-12 py-8">
    <h2 class="text-3xl font-bold mb-8 text-center text-yellow-400 animate-slideIn">
        Top Freizeitparks entdecken
    </h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($parks as $park)
            @php
                $gradients = [
                    ['from-indigo-500', 'via-indigo-600', 'to-purple-700'],
                    ['from-pink-500', 'via-pink-600', 'to-red-500'],
                    ['from-teal-500', 'via-teal-600', 'to-emerald-600'],
                    ['from-orange-500', 'via-orange-600', 'to-amber-500'],
                    ['from-purple-500', 'via-purple-600', 'to-fuchsia-600'],
                    ['from-red-500', 'via-red-600', 'to-rose-600'],
                ];
                $gradient = $gradients[$loop->index % count($gradients)];
            @endphp

            <div x-data="{ flipped: false, videoLoaded: false }"
                 class="flip-container relative w-full shadow-lg"
                 :class="{ 'flipped': flipped }"
                 tabindex="0"
                 role="region"
                 aria-label="Karte für {{ $park->name }}">

                <div class="flipper w-full h-full">

                    <!-- Vorderseite -->
                    <div class="front text-center bg-gradient-to-br {{ implode(' ', $gradient) }} p-4 rounded-xl">
                        <div class="relative mb-4 w-full h-40">
                            <img src="{{ $park->logo_url ?? $park->image }}"
                                 alt="{{ $park->name }}"
                                 class="absolute top-0 left-0 w-full h-full object-cover z-20 rounded-lg"
                                 loading="lazy"
                                 onerror="this.src='/images/park_placeholder.png';" />

                            <!-- Flip Icon -->
                            <button @click.prevent.stop="flipped = true"
                                    class="absolute top-2 right-2 z-30 bg-white/30 hover:bg-white/50 text-white text-sm px-2 py-1 rounded-full shadow transition"
                                    aria-label="Karte umdrehen">
                                ↻
                            </button>
                        </div>

                        <h2 class="text-white text-2xl font-bold">{{ \Str::words($park->name, 2, '') }}</h2>
                        <p class="text-white text-sm mt-1">{{ $park->country }}</p>

                        @if($userLat && $userLng && isset($park->distance))
                            <p class="text-sm mt-1 text-yellow-300 font-semibold">
                                {{ number_format($park->distance, 1, ',', '.') }} km entfernt
                            </p>
                        @endif

                        <!-- Status-Badge -->
                        <x-status-badge :status="$park->opening_status" class="mt-2" />

                        <!-- Mehr erfahren Button -->
                        <div class="mt-4">
                            <a href="{{ route('parks.show', $park->slug ?: $park->id) }}"
                               class="inline-block px-6 py-2 font-bold text-white bg-yellow-400 hover:bg-yellow-300 rounded shadow transition"
                               aria-label="Mehr über {{ $park->name }} erfahren">
                                Mehr erfahren
                            </a>
                        </div>
                    </div>

                    <!-- Rückseite -->
                    <div class="back bg-gray-800 p-4 text-white rounded-xl overflow-auto">
                        <h4 class="text-lg font-semibold mb-3">{{ $park->name }}</h4>

                        <x-park-video-or-description :park="$park" :video-loaded="'videoLoaded'" />

                        <button @click.prevent.stop="flipped = false"
                        class="absolute top-2 right-2 z-30 bg-white/30 hover:bg-white/50 text-white text-sm px-2 py-1 rounded-full shadow transition"
                        aria-label="Karte umdrehen">
                        ↻
                    </button>
                <button @click.prevent.stop="flipped = false"
                        type="button"
                        class="mt-4 w-full bg-white text-gray-900 font-semibold py-2 rounded-lg hover:bg-gray-100 transition-colors"
                        aria-label="Zurück zur Vorderseite">
                    Zurück
                </button>
                    </div>
                </div>
            </div>
        @empty
            <p class="col-span-full text-center text-gray-400 text-lg">Keine passenden Parks gefunden.</p>
        @endforelse
    </div>


        <div class="mt-8 flex justify-center">
            {{ $parks->links('vendor.pagination.custom') }}
        </div>

    </div>
</section>





</div>
@push('scripts')
<script>
    document.addEventListener('livewire:load', function () {
        // Scroll-Anker nach Paginierung
        Livewire.on('component.updated', () => {
            const anchor = document.getElementById('park-liste-anchor');
            if (anchor) anchor.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
</script>
@endpush
