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
<div class="hidden md:block max-w-6xl mx-auto px-6 mb-6">
    <div class="flex items-center gap-3 mb-3">
        <span class="text-sm font-semibold text-white">Status:</span>
        @foreach([
            'alle' => 'Alle',
            'open' => '🟢 Geöffnet',
            'closed' => '🔴 Geschlossen',
            'unknown' => '⚪ Unbekannt'
        ] as $key => $label)
            <button wire:click="$set('status', '{{ $key }}')"
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

    @php
    $gradients = [
        ['#6366f1', '#4f46e5'], // Indigo
        ['#ec4899', '#db2777'], // Pink
        ['#14b8a6', '#0d9488'], // Teal
        ['#f97316', '#ea580c'], // Orange
        ['#a855f7', '#9333ea'], // Purple
        ['#ef4444', '#dc2626'], // Red
    ];
    @endphp

    <style>
        .flip-container {
            perspective: 1000px;
        }
        .flipper {
            transform-style: preserve-3d;
            transition: transform 0.8s ease-in-out;
            position: relative;
        }
        .flipped .flipper {
            transform: rotateY(180deg);
        }
        .front, .back {
            backface-visibility: hidden;
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            border-radius: 1rem;
        }
        .back {
            transform: rotateY(180deg);
        }
        .animate-slideIn {
            animation: slideIn 0.3s ease-in;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <section id="park-cards" class="max-w-7xl mx-auto px-6 lg:px-12 py-8">
        <h2 class="text-3xl font-bold mb-8 text-center text-yellow-400 animate-slideIn">
            Top Freizeitparks entdecken
        </h2>
        <div class="relative z-10 px-4 sm:px-6 lg:px-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($parks as $park)
                    @php
                        $gradient = $gradients[$loop->index % count($gradients)];
                    @endphp

                    <div class="flip-container relative w-full min-h-[28rem] shadow-lg hover:shadow-xl transition-shadow duration-300"
                         x-data="{ flipped: false }" :class="{ 'flipped': flipped }">
                        <div class="flipper w-full h-full">


                            <div class="front p-6 flex flex-col justify-between text-center rounded-xl relative overflow-hidden"
                            style="background: linear-gradient(to bottom right, {{ $gradient[0] }}, {{ $gradient[1] }});">

                           <a href="{{ route('parks.show', $park->id) }}" aria-label="Details zu {{ $park->name }}">
                               <div class="relative mb-4 w-full h-40">
                                   <div class="absolute top-2 left-2 w-full h-full bg-yellow-400 shadow-xl z-10 rounded-lg"></div>
                                   <img src="{{ $park->logo_url ?? $park->image }}"
                                        alt="{{ $park->name }}"
                                        class="absolute top-0 left-0 w-full h-full object-cover z-20 rounded-lg"
                                        loading="lazy" />
                               </div>
                           </a>
                           <div class="flex-grow flex flex-col justify-center z-30 relative">
                               @php
                                   $nameWords = collect(explode(' ', $park->name))->take(2)->implode(' ');
                               @endphp
                               <h2 class="text-white text-3xl leading-tight font-bold"
                                   style="font-family: 'Bowlby One', sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5); letter-spacing: 2px;">
                                   {{ $nameWords }}
                               </h2>
                               <p class="text-white font-medium mt-1"
                                  style="-webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; text-shadow: 0 1px 1px rgba(0, 0, 0, 0.3); letter-spacing: 1px;">
                                   {{ $park->country }}
                               </p>
                               <p class="text-sm mt-2 font-semibold {{ $park->status_class }}"
                                  style="-webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; text-shadow: 0 1px 1px rgba(0, 0, 0, 0.3); letter-spacing: 1px;">
                                   {{ $park->status_label }}
                               </p>
                           </div>
                           <div class="relative mt-6 w-full">
                               <a href="#" @click.prevent="flipped = !flipped"
                                  class="relative inline-block px-6 py-2 font-bold text-white bg-yellow-400 hover:bg-yellow-300 rounded shadow transition"
                                  style="font-family: 'Bowlby One', sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; letter-spacing: 1.5px;">
                                   Mehr erfahren
                               </a>
                           </div>
                       </div>

                            <div class="back bg-gray-800 text-white p-6 flex flex-col justify-between rounded-xl">
                                <div class="flex-grow">
                                    <h4 class="text-lg font-semibold mb-3">{{ $park->name }}</h4>
                                    @if($park->video_url)
                                        <div class="aspect-w-16 aspect-h-9 mb-4">
                                            @php $video = $park->video_url; @endphp
                                            @if(Str::contains($video, 'youtube'))
                                                <iframe src="https://www.youtube.com/embed/{{ Str::afterLast($video, 'v=') }}"
                                                        frameborder="0" allowfullscreen class="w-full h-full rounded-lg" loading="lazy"></iframe>
                                            @elseif(Str::contains($video, 'vimeo'))
                                                <iframe src="https://player.vimeo.com/video/{{ Str::afterLast($video, '/') }}"
                                                        frameborder="0" allowfullscreen class="w-full h-full rounded-lg" loading="lazy"></iframe>
                                            @elseif(Str::endsWith($video, '.mp4'))
                                                <video class="w-full rounded-lg" controls muted loop>
                                                    <source src="{{ $video }}" type="video/mp4">
                                                </video>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-300 mb-4">{{ \Str::limit($park->description, 80) }}</p>
                                    @endif
                                </div>
                                <button @click="flipped = !flipped"
                                        class="mt-4 w-full bg-white text-gray-900 font-semibold py-2 rounded-lg hover:bg-gray-100 transition-colors">
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
        // Nach dem Paginieren zum Anfang der Karten scrollen
        Livewire.on('component.updated', () => {
            const parkCards = document.getElementById('park-cards');
            if (parkCards) {
                parkCards.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });

        // Trigger-Event, wenn die Seite gewechselt wird
        document.addEventListener('click', function (e) {
            if (e.target.closest('[wire\\:click^="gotoPage"], [wire\\:click="previousPage"], [wire\\:click="nextPage"]')) {
                // Optional: Du kannst hier ein Event auslösen, falls nötig
            }
        });
    });
</script>
@endpush
