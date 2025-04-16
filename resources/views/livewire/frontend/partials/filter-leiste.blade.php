<div class="space-y-4">

    <!-- Suchfeld -->
    <input
        type="text"
        wire:model.live="suche"
        placeholder="Suche nach Freizeitpark oder Ort..."
        class="w-full px-4 py-3 border border-gray-300 rounded-lg
               focus:outline-none focus:ring focus:border-blue-400
               transition duration-300 ease-in-out"
    />

    <!-- Filter-Chips -->
    @if($land || $status !== 'alle')
        <div class="flex flex-wrap gap-2 text-sm animate-fadeIn">
            @if($land)
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full flex items-center">
                    {{ $land }}
                    <button wire:click="$set('land', '')" class="ml-2 text-blue-500 hover:text-blue-700 transition">&times;</button>
                </span>
            @endif
            @if($status !== 'alle')
                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full flex items-center">
                    {{ ucfirst($status) }}
                    <button wire:click="$set('status', 'alle')" class="ml-2 text-green-500 hover:text-green-700 transition">&times;</button>
                </span>
            @endif

            <button wire:click="$set('land', ''); $set('status', 'alle')"
                    class="ml-4 underline text-gray-600 hover:text-black transition">
                Alle Filter zurücksetzen
            </button>
        </div>
    @endif

    <!-- Filter-Buttons (Desktop) -->
    <div class="hidden md:flex flex-wrap gap-4 items-center">

        <!-- Länder -->
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

        <!-- Status -->
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-sm font-medium text-gray-700 mr-2">Status:</span>

            @foreach(['alle' => 'Alle', 'geöffnet' => '🟢 Geöffnet', 'geschlossen' => '🔴 Geschlossen'] as $key => $label)
                <button wire:click="$set('status', '{{ $key }}')"
                        class="px-3 py-1 rounded-full border text-sm transition transform
                        {{ $status === $key ? 'bg-green-600 text-white border-green-600' : 'text-gray-700 border-gray-300 hover:bg-gray-100 hover:scale-105' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Mobile Filter -->
    <div class="md:hidden grid grid-cols-1 gap-4">
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
                <option value="geöffnet">🟢 Geöffnet</option>
                <option value="geschlossen">🔴 Geschlossen</option>
            </select>
        </div>
    </div>
</div>
