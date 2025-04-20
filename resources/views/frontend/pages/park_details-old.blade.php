@extends('frontend.layouts.app')

@section('content')

<section class="relative h-64 sm:h-96 mb-8">
    <img loading="lazy" src="{{ $park->image ? asset($park->image) : asset('images/park-placeholder.jpg') }}" alt="{{ $park->name }}" class="w-full h-full object-cover rounded-lg">
    <div class="absolute inset-0 bg-black/30 flex items-end">
        <h1 class="text-2xl sm:text-4xl md:text-5xl font-bold text-white p-4 sm:p-6">{{ $park->name }}</h1>
    </div>
</section>

<div class="elementor-shape elementor-shape-bottom" data-negative="false">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 283.5 19.6" preserveAspectRatio="none">
<path class="elementor-shape-fill" style="opacity:0.33" d="M0 0L0 18.8 141.8 4.1 283.5 18.8 283.5 0z"></path>
<path class="elementor-shape-fill" style="opacity:0.33" d="M0 0L0 12.6 141.8 4 283.5 12.6 283.5 0z"></path>
<path class="elementor-shape-fill" style="opacity:0.33" d="M0 0L0 6.4 141.8 4 283.5 6.4 283.5 0z"></path>
<path class="elementor-shape-fill" d="M0 0L0 1.2 141.8 4 283.5 1.2 283.5 0z"></path>
</svg>		</div>


<section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="md:col-span-2">
        <h2 class="text-xl sm:text-2xl font-semibold mb-4">Über den Park</h2>
        <p class="text-gray-600 text-sm sm:text-base">{!! $park->description ?? 'Keine Beschreibung verfügbar.' !!}</p>
    </div>
    <div class="bg-gray-100 p-4 sm:p-6 rounded-lg">
        <h3 class="text-base sm:text-lg font-semibold mb-4">Schnellinfo</h3>
        <ul class="space-y-2 text-xs sm:text-sm text-gray-700">
            <li><strong>Ort:</strong> {{ $park->location ?? 'Unbekannt' }}</li>
            <li><strong>Status:</strong>
                <span class="{{ $park->status_class }}">
                    {{ $park->status_label }}
                </span>
            </li>
            <li><strong>Bewertung:</strong> {{ $park->rating ?? 'Keine' }} ({{ $park->reviews_count ?? '0' }} Bewertungen)</li>
            @php
            $count = $park->queueTimes->count();
            $attrText = match(true) {
                $count >= 50 => "$count Fahrgeschäfte – ein Tag reicht kaum!",
                $count >= 30 => "$count Highlights voller Spaß & Action",
                $count >= 10 => "$count Attraktionen für die ganze Familie",
                default      => "$count Attraktionen – klein, aber fein",
            };
            @endphp
            <li><strong>Erlebniswelt:</strong> {{ $attrText }}</li>
            <li><strong>Geöffnet:</strong> {{ $park->queueTimes->where('is_open', true)->count() }} von {{ $park->queueTimes->count() }}</li>
            <li><strong>Coolness:</strong> {{ $park->coolness ?? 'Unbekannt' }}%</li>
        </ul>
    </div>
</section>

<section class="mb-8">
    <h2 class="text-xl sm:text-2xl font-semibold mb-4">Öffnungszeiten</h2>
    <div class="bg-white shadow-md rounded-lg p-4 sm:p-6">
        <p class="text-sm sm:text-base">Heute geöffnet von {{ $park->opening_hours ?? 'Nicht bekannt' }}</p>
    </div>
</section>




<x-rating-stars :rating="2.5" />

<x-rating-badge :rating="3.5" />








<livewire:frontend.park-andrang-component :park="$park" />


<section class="mb-8" x-data="{ status: 'all', search: '', wait: 'all' }">
    <h2 class="text-xl sm:text-2xl font-semibold mb-4">Attraktionen</h2>

    @if ($park->queueTimes->isEmpty())
        <p class="text-gray-600 text-sm sm:text-base">Keine Attraktionen verfügbar.</p>
    @else
        <!-- Filterleiste -->
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <select x-model="status" class="px-4 py-2 border rounded text-sm w-full sm:w-auto">
                <option value="all">Alle Status</option>
                <option value="open">Geöffnet</option>
                <option value="closed">Geschlossen</option>
            </select>
            <select x-model="wait" class="px-4 py-2 border rounded text-sm w-full sm:w-auto">
                <option value="all">Alle Wartezeiten</option>
                <option value="short">Kurz (0–10)</option>
                <option value="medium">Mittel (11–30)</option>
                <option value="long">Lang (31–59)</option>
                <option value="verylong">Sehr lang (60+)</option>
            </select>
            <input x-model="search" type="text" placeholder="Suche nach Name…" class="px-4 py-2 border rounded text-sm w-full">
        </div>

        <!-- Attraktionenliste -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-4 lg:gap-6">
            @foreach ($park->queueTimes as $ride)
                @php
                    $wait = $ride->wait_time ?? 0;
                    $waitClass = match(true) {
                        $wait <= 10 => 'text-green-600',
                        $wait <= 30 => 'text-yellow-500',
                        $wait <= 59 => 'text-orange-500',
                        default     => 'text-red-600',
                    };
                @endphp
                <div
                    x-show="(status === 'all' || status === '{{ $ride->is_open ? 'open' : 'closed' }}')
                    && (wait === 'all'
                        || (wait === 'short' && {{ $wait }} <= 10)
                        || (wait === 'medium' && {{ $wait }} > 10 && {{ $wait }} <= 30)
                        || (wait === 'long' && {{ $wait }} > 30 && {{ $wait }} <= 59)
                        || (wait === 'verylong' && {{ $wait }} >= 60))
                    && (@js(strtolower($ride->ride_name)).includes(search.toLowerCase()))"
                    class="w-full box-border bg-white shadow-md rounded-lg overflow-hidden border-l-4 {{ $waitClass }}"
                >
                    <div class="p-4">
                        <h3 class="text-base sm:text-lg font-semibold break-words">{{ $ride->ride_name }}</h3>
                        <p class="text-xs sm:text-sm text-gray-500">
                            Wartezeit: <strong class="{{ $waitClass }}">{{ $wait }} Minuten</strong><br>
                            Status:
                            <span class="text-{{ $ride->is_open ? 'green' : 'red' }}-600">
                                {{ $ride->is_open ? 'Geöffnet' : 'Geschlossen' }}
                            </span>
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>


@if($nearbyParks->count())
<section class="mb-12 px-2 sm:px-4">
    <h2 class="text-xl sm:text-2xl font-semibold mb-6">Aktivitäten in der Nähe</h2>
    <div class="swiper nearbySwiper">
        <div class="swiper-wrapper">
            @foreach($nearbyParks as $nearby)
            <div class="swiper-slide">
                <div class="bg-white rounded-lg shadow-md overflow-hidden w-full max-w-xs sm:max-w-[260px]">
                    <img loading="lazy" src="{{ asset($nearby->image ?? 'images/park-placeholder.jpg') }}" class="w-full h-40 object-cover" alt="{{ $nearby->name }}">
                    <div class="p-4">
                        <h3 class="text-base sm:text-lg font-semibold">{{ $nearby->name }}</h3>
                        <p class="text-xs sm:text-sm text-gray-500">{{ round($nearby->distance, 1) }} km entfernt</p>
                        <a href="{{ route('parks.show', $nearby->id) }}" class="text-blue-600 text-sm mt-2 inline-block">Details ansehen →</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif



@endsection

