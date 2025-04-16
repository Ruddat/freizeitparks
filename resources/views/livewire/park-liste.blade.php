<div>
    <!-- Suchfeld -->
    <div class="mb-6">
        <input
            type="text"
            wire:model.live="suche"
            placeholder="Suche nach Freizeitpark, Ort oder Land..."
            class="w-full px-4 py-3 border border-gray-300 rounded-lg
                   focus:outline-none focus:ring focus:border-blue-400
                   transition duration-300 ease-in-out"
        />
    </div>

    <!-- Aktive Filter-Chips -->
    @if($suche || $land || $status !== 'alle')
        <div class="mb-4 flex flex-wrap items-center gap-2 text-sm animate-fadeIn">
            @if($suche)
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full flex items-center">
                    Suche: {{ $suche }}
                    <button wire:click="$set('suche', '')" class="ml-2 text-blue-500 hover:text-blue-700 transition">×</button>
                </span>
            @endif

            @if($land)
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full flex items-center">
                    Land: {{ $land }}
                    <button wire:click="$set('land', '')" class="ml-2 text-blue-500 hover:text-blue-700 transition">×</button>
                </span>
            @endif

            @if($status !== 'alle')
                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full flex items-center">
                    Status: {{ match($status) {
                        'open' => 'Geöffnet',
                        'closed' => 'Geschlossen',
                        'unknown' => 'Unbekannt',
                    } }}
                    <button wire:click="$set('status', 'alle')" class="ml-2 text-green-500 hover:text-green-700 transition">×</button>
                </span>
            @endif

            <button wire:click="resetFilter"
                    class="ml-4 underline text-gray-600 hover:text-black transition">
                Alle Filter zurücksetzen
            </button>
        </div>
    @endif

    <!-- Länder-Filter (dynamisch) -->
    <div class="flex flex-wrap items-center gap-2">
        <span class="text-sm font-medium text-gray-700 mr-2">Land:</span>

        <button wire:click="$set('land', '')"
                class="px-3 py-1 rounded-full border text-sm transition transform
                {{ $land === '' ? 'bg-blue-600 text-white border-blue-600' : 'text-gray-700 border-gray-300 hover:bg-gray-100 hover:scale-105' }}">
            Alle
        </button>

        @foreach($laender as $landOption)
            <button wire:click="$set('land', '{{ $landOption }}')"
                    class="px-3 py-1 rounded-full border text-sm transition transform
                    {{ $land === $landOption ? 'bg-blue-600 text-white border-blue-600' : 'text-gray-700 border-gray-300 hover:bg-gray-100 hover:scale-105' }}">
                {{ $landOption }}
            </button>
        @endforeach
    </div>

    <!-- Status-Filter -->
    <div class="flex flex-wrap items-center gap-2 mt-4 mb-4">
        <span class="text-sm font-medium text-gray-700 mr-2">Status:</span>
        @foreach([
            'alle' => 'Alle',
            'open' => '🟢 Geöffnet',
            'closed' => '🔴 Geschlossen',
            'unknown' => '⚪ Unbekannt'
        ] as $key => $label)
            <button wire:click="$set('status', '{{ $key }}')"
                    class="px-3 py-1 rounded-full border text-sm transition transform
                    {{ $status === $key ? 'bg-green-600 text-white border-green-600' : 'text-gray-700 border-gray-300 hover:bg-gray-100 hover:scale-105' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <!-- Mobile-Filter -->
    <div class="md:hidden grid grid-cols-1 gap-4 mb-6 mt-4">
        <div>
            <label class="block text-sm font-medium mb-1">Land</label>
            <select wire:model="land" class="w-full border border-gray-300 p-2 rounded">
                <option value="">Alle Länder</option>
                @foreach($laender as $landOption)
                    <option value="{{ $landOption }}">{{ $landOption }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <select wire:model="status" class="w-full border border-gray-300 p-2 rounded">
                <option value="alle">Alle</option>
                <option value="open">🟢 Geöffnet</option>
                <option value="closed">🔴 Geschlossen</option>
                <option value="unknown">⚪ Unbekannt</option>
            </select>
        </div>
    </div>

<!-- Parkliste -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-12">
    @forelse($parks as $park)
        <div class="perspective w-full md:w-96">
            <div onclick="this.classList.toggle('rotate-y-180')" class="flip-card relative w-full h-[30rem] transition-transform duration-700 transform-style preserve-3d cursor-pointer">
                {{-- Vorderseite --}}
                <div class="absolute inset-0 backface-hidden bg-white rounded-xl shadow-md overflow-hidden flex flex-col items-center text-center p-6 z-10">
                    <img src="{{ $park->logo_url ?? $park->image }}" alt="{{ $park->name }}" class="w-full h-40 object-contain mb-4">
                    <h3 class="text-xl font-bold text-gray-900">{{ $park->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $park->country }}</p>
                    <p class="text-sm mt-2 {{ $park->status_class }}">{{ $park->status_label }}</p>
                    <p class="mt-4 text-sm text-gray-700">{{ \Str::limit($park->description, 90) }}</p>
                </div>

                {{-- Rückseite --}}
                <div class="absolute inset-0 backface-hidden rotate-y-180 bg-yellow-50 rounded-xl shadow-md text-gray-800 p-6 flex flex-col justify-between z-20">
                    <div>
                        <h4 class="text-xl font-bold text-gray-900 mb-2">Auf einen Blick</h4>
                        <div class="flex items-center text-yellow-500 mb-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= round($park->rating) ? 'fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09L5.64 12.18.762 7.91l6.14-.89L10 1l3.098 6.02 6.14.89-4.878 4.27 1.518 5.91z"/></svg>
                            @endfor
                            <span class="text-sm text-gray-600 ml-2">{{ number_format($park->rating, 1) }} Bewertungen</span>
                        </div>
                        <ul class="text-sm list-disc list-inside space-y-1 text-gray-700">
                            <li>Coolness: {{ $park->coolness }}%</li>
                            <li>Status: {{ $park->status_label }}</li>
                        </ul>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('parks.show', $park->id) }}" class="block w-full text-center bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold py-2 rounded">
                            MEHR ERFAHREN
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <p class="col-span-full text-center text-gray-500">Keine passenden Parks gefunden.</p>
    @endforelse
</div>

</div>
