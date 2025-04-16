<div class="w-full md:w-80 perspective">
    <div class="relative w-full h-96 transition-transform duration-500 transform-style preserve-3d group hover:rotate-y-180">
        {{-- Front --}}
        <div class="absolute inset-0 bg-white rounded-xl shadow-lg p-4 backface-hidden flex flex-col items-center justify-between">
            <img src="{{ $park->logo_url }}" alt="{{ $park->title }}" class="w-48 h-32 object-contain mb-2">
            <h3 class="text-lg font-bold text-center">{{ $park->title }}</h3>
            <p class="text-sm text-gray-500 text-center">{{ $park->country }}</p>
            <p class="text-sm mt-2 text-center">{{ Str::limit($park->description, 60) }}</p>
            <p class="mt-4 text-xs text-gray-700">⏰ {{ $park->opening_times_today ?? 'Öffnungszeiten nicht verfügbar' }}</p>
            <button class="mt-4 bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700 hidden md:block">
                Rückseite
            </button>
        </div>

        {{-- Back --}}
        <div class="absolute inset-0 bg-gray-900 text-white rounded-xl shadow-lg p-4 rotate-y-180 backface-hidden flex flex-col justify-between">
            {{-- Video --}}
            @if($park->video_url)
                <div class="aspect-video mb-4">
                    @include('components.video-embed', ['url' => $park->video_url])
                </div>
            @endif

            {{-- Bewertung / Coolness --}}
            <div>
                <p class="text-sm">⭐ {{ number_format($park->rating, 1) }} / 5</p>
                <p class="text-sm">🔥 Coolness: {{ $park->coolness }}%</p>
            </div>

            <div class="mt-auto space-y-2">
                <a href="{{ route('parks.show', $park->id) }}" class="block bg-white text-blue-700 text-center rounded px-4 py-2 text-sm hover:bg-blue-100">
                    Details ansehen
                </a>
                @if($park->website)
                    <a href="{{ $park->website }}" target="_blank" class="block bg-blue-700 text-white text-center rounded px-4 py-2 text-sm hover:bg-blue-800">
                        Zur offiziellen Website
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
