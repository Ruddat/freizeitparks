@props(['park', 'gradient'])

<div class="flip-container relative w-full h-[28rem]" x-data="{ flipped: false }" :class="{ 'flipped': flipped }">
    <div class="flipper w-full h-full">
        <!-- Vorderseite -->
        <div class="front bg-gradient-to-br {{ $gradient[0] }} {{ $gradient[1] }} p-6 flex flex-col justify-between text-center shadow-xl rounded-xl">
            <a href="{{ route('parks.show', $park->id) }}" aria-label="Details zu {{ $park->name }}">
                <img
                    src="{{ $park->logo_url ?? $park->image }}"
                    alt="{{ $park->name }}"
                    class="w-full h-40 object-cover rounded-md mb-4 border-2 border-white shadow-sm"
                    loading="lazy"
                >
            </a>
            <div class="flex-grow flex flex-col justify-center">
                <h3 class="text-xl font-bold text-white uppercase leading-tight">
                    {{ $park->name }}
                </h3>
                <p class="text-white text-opacity-90 font-medium mt-1">
                    {{ $park->country }}
                </p>
                <p class="text-sm mt-2 font-semibold {{ $park->status_class }}">
                    {{ $park->status_label }}
                </p>
            </div>
            <button
                @click="flipped = !flipped"
                class="mt-4 w-full text-yellow-700 bg-white hover:bg-gray-100 font-semibold py-2 rounded-lg transition-colors"
                aria-label="Mehr über {{ $park->name }} erfahren"
            >
                Mehr erfahren
            </button>
        </div>

        <!-- Rückseite -->
        <div class="back bg-gray-800 text-white p-6 flex flex-col justify-between shadow-xl rounded-xl">
            <div class="flex-grow">
                <h4 class="text-lg font-semibold mb-2">{{ $park->name }}</h4>
                @if($park->video_url)
                    <div class="aspect-w-16 aspect-h-9 mb-4">
                        @php
                            $video = $park->video_url;
                        @endphp
                        @if(Str::contains($video, 'youtube'))
                            <iframe
                                src="https://www.youtube.com/embed/{{ Str::afterLast($video, 'v=') }}"
                                frameborder="0"
                                allowfullscreen
                                class="w-full h-full rounded"
                                loading="lazy"
                            ></iframe>
                        @elseif(Str::contains($video, 'vimeo'))
                            <iframe
                                src="https://player.vimeo.com/video/{{ Str::afterLast($video, '/') }}"
                                frameborder="0"
                                allowfullscreen
                                class="w-full h-full rounded"
                                loading="lazy"
                            ></iframe>
                        @elseif(Str::endsWith($video, '.mp4'))
                            <video class="w-full rounded" controls muted loop>
                                <source src="{{ $video }}" type="video/mp4">
                            </video>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-300 mb-4">{{ \Str::limit($park->description, 80) }}</p>
                @endif
            </div>
            <button
                @click="flipped = !flipped"
                class="mt-4 w-full bg-white text-gray-900 font-semibold py-2 rounded hover:bg-gray-100 transition-colors"
                aria-label="Zurück zu {{ $park->name }}"
            >
                Zurück
            </button>
        </div>
    </div>
</div>
