@extends('frontend.layouts.app')

@section('content')












<div class="bg-[#0d0f3f] text-white font-sans">
    {{-- Hero Section
    <section class="text-center py-16 px-4 bg-gradient-to-b from-[#15002a] to-[#0d0f3f]">
      <h1 class="text-4xl md:text-5xl font-bold mb-4">EUROPA-PARK</h1>
      <a href="#" class="inline-block mt-4 px-6 py-3 bg-[#1e869e] hover:bg-[#16697a] text-white rounded-full font-semibold">Website besuchen</a>
      <p class="mt-6 max-w-2xl mx-auto text-lg text-gray-300">
        Europa-Park ist ein Freizeitpark in Rust, Baden-Württemberg. Der 1975 eröffnete Park gehört zu den größten Freizeitparks in Europa und bietet eine Vielzahl an Attraktionen und Shows für die ganze Familie.
      </p>
    </section>
--}}


{{-- Wettervorhersage --}}
@if($forecast->isNotEmpty())
    <section class="weather-forecast relative py-8 md:py-16 px-4 bg-gradient-to-br from-[#010b3f] to-black/60 overflow-hidden">

        {{-- 🌥️ Dekoration --}}
        <div class="absolute top-[-40px] left-[-60px] w-[260px] h-[260px] opacity-10 bg-no-repeat bg-contain pointer-events-none"
             style="background-image: url('{{ asset('images/weather/animated/cloudy.svg') }}')"></div>
        <div class="absolute bottom-[-60px] right-[-60px] w-[220px] h-[220px] opacity-25 bg-no-repeat bg-contain pointer-events-none"
             style="background-image: url('{{ asset('images/weather/animated/clear-day.svg') }}')"></div>

        {{-- 🏰 Watermark --}}
        <div class="absolute inset-0 flex justify-center items-end z-0 pointer-events-none select-none">
            <p class="text-white/5 text-[80px] md:text-[140px] font-extrabold tracking-widest uppercase leading-none text-center whitespace-nowrap shimmer">
                {{ strtoupper($park->name) }} {{ now()->year }}
            </p>
        </div>

        {{-- 🌤️ Titel --}}
        <h2 class="text-2xl md:text-3xl font-extrabold text-white text-center mb-6 md:mb-10 z-10 relative flex flex-col items-center">
            <span class="text-yellow-300 text-3xl md:text-4xl drop-shadow-lg">🌦️</span>
            <span>{{ __('messages.weather_forecast') ?? 'Wettervorhersage' }}</span>
            <span class="text-xs md:text-sm mt-1 text-white/70 font-light">für {{ $park->name }} · {{ now()->year }}</span>
        </h2>

        {{-- 🌈 Forecast Cards --}}
        <div class="forecast-cards max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-5 gap-4 md:gap-6 text-center relative z-10">
            @foreach($forecast->take(5) as $day)
                <div class="forecast-card relative group bg-[#2a2d7f]/60 border border-white/10 rounded-2xl p-4 md:p-5 text-white shadow-lg backdrop-blur-xl transition-transform duration-300 hover:scale-105 overflow-hidden">
                    {{-- Vorderseite --}}
                    <div class="relative z-10 space-y-2 group-hover:opacity-30 transition-opacity duration-300">
                        <div class="date text-sm font-medium tracking-wide text-white/80">
                            <span class="mobile-only">Heute</span>
                            <span class="desktop-only">{{ $day['date'] }}</span>
                        </div>
                        <div class="my-2 h-16 flex justify-center items-center">
                            <lottie-player
                                src="{{ $day['icon'] }}"
                                background="transparent"
                                speed="1"
                                loop
                                autoplay
                                style="height: 56px; width: 56px"
                                class="drop-shadow-md group-hover:animate-bounce"
                            ></lottie-player>
                        </div>
                        <div class="text-red-300 font-extrabold text-2xl">{{ $day['temp_day'] }}°C</div>
                        <div class="extra-info text-sm text-blue-200 desktop-only">{{ __('messages.at_night') ?? 'nachts' }} {{ $day['temp_night'] }}°C</div>
                        <div class="description text-xs mt-2 text-white/80 italic desktop-only">{{ $day['description'] }}</div>
                    </div>

                    {{-- Hover-Details mit dynamischem Text (nur auf Desktop) --}}
                    <div class="hover-details absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-center items-center bg-black/80 text-white text-xs md:text-sm p-4 rounded-2xl z-20 backdrop-blur-md desktop-only">
                        <p class="text-pink-400 font-semibold text-sm mb-2">{{ $day['description'] }}</p>
                        <p class="text-yellow-300 text-xs italic mb-2">
                            @php
                                $weatherTip = match ($day['weather_code']) {
                                    1000 => 'Perfekt für einen Ausflug!',
                                    1003 => 'Das richtige Park-Wetter.',
                                    1006, 1009 => 'Etwas bewölkt, aber machbar!',
                                    1063, 1183, 1189, 1195, 1240, 1243 => 'Nimm die Regenjacke mit!',
                                    1273, 1087 => 'Vorsicht, Gewitter möglich!',
                                    1066, 1213, 1219, 1225 => 'Schnee? Warm anziehen!',
                                    1030 => 'Nebel – vorsichtig fahren!',
                                    default => 'Plane nach Gefühl!',
                                };
                                echo $weatherTip;
                            @endphp
                        </p>
                        <ul class="space-y-1 text-white/90">
                            <li>💨 Wind: <strong>{{ $day['wind_speed'] ?? '?' }} km/h</strong></li>
                            <li>🌧️ Regen: <strong>{{ $day['rain_chance'] ?? '?' }}%</strong></li>
                            <li>🔆 UV-Index: <strong>{{ $day['uv_index'] ?? '?' }}</strong></li>
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Sanfter Verlauf --}}
        <div class="absolute bottom-0 left-0 w-full h-16 md:h-24 bg-gradient-to-t from-[#1c1e5c] to-transparent z-0 pointer-events-none"></div>

        {{-- Lottie Player CDN --}}

        <style>
            /* Mobile First Ansatz */
            .forecast-card:not(:first-child) {
                display: none;
            }

            .mobile-only {
                display: inline;
            }

            .desktop-only {
                display: none;
            }

            .hover-details {
                display: none;
            }

            /* Desktop Styles */
            @media (min-width: 768px) {
                .forecast-cards {
                    grid-template-columns: repeat(5, 1fr);
                }

                .forecast-card {
                    display: block !important;
                }

                .mobile-only {
                    display: none;
                }

                .desktop-only {
                    display: block;
                }

                .extra-info,
                .description {
                    display: block;
                }

                .hover-details {
                    display: flex;
                }
            }
        </style>
    </section>
@endif
{{-- Wettervorhersage Ende --}}






    {{-- Hero-Bereich --}}



    {{-- Navigation --}}
    <nav class="w-full sticky top-0 z-50 bg-gradient-to-r from-purple-800 via-indigo-800 to-purple-900 text-white px-4 py-2 shadow-lg">
        <div class="max-w-screen-xl mx-auto flex flex-wrap justify-center items-center gap-3 text-sm font-medium">
            <!-- Parkname mit Icon -->
            <span class="inline-flex items-center gap-2 bg-purple-700/80 px-4 py-2 rounded-full backdrop-blur-sm border border-purple-500/30">
                <span class="text-purple-200">🏰</span>
                <span>{{ $park->title }}</span>
            </span>

            {{-- Webseite
            <a href="{{ $park->website_url }}" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-indigo-600/80 hover:bg-indigo-500 rounded-full transition-all duration-300 ease-in-out backdrop-blur-sm border border-indigo-400/30">
                <span class="text-indigo-200">🌐</span>
                <span>Website</span>
            </a>

            --}}

            <!-- Live Wartezeiten (Anker) -->
            @if($park->queueTimes->isNotEmpty())
            <a href="#wartezeiten" class="flex items-center gap-2 px-4 py-2 bg-cyan-500/80 hover:bg-cyan-400 rounded-full transition-all duration-300 ease-in-out backdrop-blur-sm border border-cyan-300/30 animate-pulse-glow">
                <span class="text-cyan-200">⏳</span>
                <span>Live Wartezeiten</span>
            </a>
            @endif


            <!-- Besucherzahl (Anker) -->
            @if($visits24h > 0)
                <a href="#besucher" class="flex items-center gap-2 px-4 py-2 bg-red-600/80 hover:bg-red-500 rounded-full transition-all duration-300 ease-in-out backdrop-blur-sm border border-red-400/30">
                    <span class="text-red-200">🔥</span>
                    <span>{{ $visits24h }}x besucht</span>
                </a>
            @endif

            <!-- Anfahrt -->
            <a href="https://maps.google.com/?q={{ $park->latitude }},{{ $park->longitude }}" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-green-600/80 hover:bg-green-500 rounded-full transition-all duration-300 ease-in-out backdrop-blur-sm border border-green-400/30">
                <span class="text-green-200">📍</span>
                <span>Anfahrt</span>
            </a>

            <!-- Öffnungszeiten (Anker) -->
            <a href="#oeffnungszeiten" class="flex items-center gap-2 px-4 py-2 bg-yellow-500/80 hover:bg-yellow-400 text-gray-900 rounded-full transition-all duration-300 ease-in-out backdrop-blur-sm border border-yellow-300/30">
                <span class="text-yellow-200">🕒</span>
                <span>Info</span>
            </a>



            <!-- Bewertung (Anker) -->
            <a href="#bewertungen" class="flex items-center gap-2 px-4 py-2 bg-pink-600/80 hover:bg-pink-500 rounded-full transition-all duration-300 ease-in-out backdrop-blur-sm border border-pink-400/30">
                <span class="text-pink-200">⭐</span>
                <span>Jetzt bewerten</span>
            </a>
        </div>
    </nav>

    <style>
        @keyframes glow {
            0% { box-shadow: 0 0 5px rgba(34, 211, 238, 0.5); }
            50% { box-shadow: 0 0 20px rgba(34, 211, 238, 0.8), 0 0 30px rgba(34, 211, 238, 0.5); }
            100% { box-shadow: 0 0 5px rgba(34, 211, 238, 0.5); }
        }

        .animate-pulse-glow {
            animation: glow 2s infinite ease-in-out;
        }
    </style>

    {{-- Park Details --}}


<!-- Info-Bereich mit Beschreibung, Bewertungen, Öffnungszeiten und Karte -->
<section class="py-12 px-4 bg-[#0d0f3f]">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-8">

<!-- Linke Spalte: Beschreibung -->
<div class="border border-gray-500 p-4 rounded-lg" id="park-description">
    <h2 class="text-2xl font-bold mb-4">Über den Freizeitpark</h2>

    <div class="text-base leading-relaxed text-gray-100 space-y-4
      [&_h3]:text-xl [&_h3]:font-bold [&_h3]:mt-6
      [&_ul]:list-disc [&_ul]:pl-6 [&_a]:text-pink-400 [&_a:hover]:underline [&_strong]:font-semibold">

      <!-- Sichtbarer Text -->
      <div id="short-text">
        {!! Str::words($park->description, 300, '...') !!}
      </div>

      <!-- Vollständiger Text -->
      <div id="full-text" class="hidden">
        {!! $park->description !!}
      </div>

      <!-- Button -->
      <button id="read-more-btn" class="text-pink-400 hover:underline focus:outline-none transition font-medium">
        Mehr lesen
      </button>
    </div>
  </div>
  <script>
    document.getElementById('read-more-btn').addEventListener('click', function () {
      const shortText = document.getElementById('short-text');
      const fullText = document.getElementById('full-text');
      const button = this;
      const container = document.getElementById('park-description');

      if (fullText.classList.contains('hidden')) {
        fullText.classList.remove('hidden');
        shortText.classList.add('hidden');
        button.textContent = 'Weniger anzeigen';

        // Nach unten scrollen – smooth zur Description-Box
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
      } else {
        fullText.classList.add('hidden');
        shortText.classList.remove('hidden');
        button.textContent = 'Mehr lesen';

        // Scroll zurück zum Anfang der Beschreibung
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
    </script>
      <!-- Rechte Spalte: Bewertungen, Öffnungszeiten, Karte -->
      <div class="space-y-6">


        @php
        use Carbon\Carbon;

        Carbon::setLocale(app()->getLocale());

        $timezone = $park->timezone && in_array($park->timezone, timezone_identifiers_list())
            ? $park->timezone
            : config('app.timezone');

        $now = Carbon::now($timezone);
        $dayLabel = $now->translatedFormat('l');

        $oeffnung = $park->openingHoursToday;
        $openTime = $oeffnung?->open ? Carbon::createFromFormat('H:i:s', $oeffnung->open, $timezone) : null;
        $closeTime = $oeffnung?->close ? Carbon::createFromFormat('H:i:s', $oeffnung->close, $timezone) : null;

        $isOpen = $openTime && $closeTime && $now->between($openTime, $closeTime);

        // ✅ Fix: Nur zukünftige Öffnungszeiten berücksichtigen
        $nextOpening = $park->openingHours
            ->filter(function ($h) use ($now, $timezone) {
                if (!$h->open || !$h->close) return false;

                $date = Carbon::createFromFormat('Y-m-d', $h->date, $timezone);
                $start = Carbon::createFromFormat('H:i:s', $h->open, $timezone)->setDateFrom($date);

                return $start->greaterThan($now);
            })
            ->sortBy('date')
            ->first();

        $nextOpeningLabel = null;
        if ($nextOpening) {
            $nextDate = Carbon::createFromFormat('Y-m-d', $nextOpening->date, $timezone);
            $nextOpen = Carbon::createFromFormat('H:i:s', $nextOpening->open, $timezone);
            $nextClose = Carbon::createFromFormat('H:i:s', $nextOpening->close, $timezone);

            $nextOpeningLabel = $nextDate->translatedFormat('l') . ', ' . $nextOpen->format('H:i') . ' – ' . $nextClose->format('H:i');
        }
    @endphp

    <div class="bg-[#1c1e5c] rounded-xl p-6 shadow text-white">
        <h3 class="text-xl font-semibold mb-2">
            🕒 Öffnungszeiten heute ({{ $dayLabel }})
        </h3>


        <div x-data="{
            now: '{{ $now->format('H:i') }}',
            updatedAt: Date.now(),
            init() {
                setInterval(() => {
                    const date = new Date();
                    const options = { timeZone: '{{ $timezone }}', hour: '2-digit', minute: '2-digit', hour12: false };
                    this.now = date.toLocaleTimeString([], options).slice(0,5);
                    this.updatedAt = Date.now(); // ➔ triggert Animation
                }, 60000);
            }
        }" class="flex items-center gap-2 text-yellow-300 text-sm mb-4"
    >
        <!-- Uhr-Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform transition-transform duration-500"
             :class="{ 'rotate-360': updatedAt }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 6v6l4 2m4-2a8 8 0 11-16 0 8 8 0 0116 0z" />
        </svg>

        <!-- Uhrzeit mit leichtem Fading -->
        <span x-text="now"
              x-transition:enter="transition ease-out duration-500"
              x-transition:enter-start="opacity-0"
              x-transition:enter-end="opacity-100"
              x-transition:leave="transition ease-in duration-500"
              x-transition:leave-start="opacity-100"
              x-transition:leave-end="opacity-0"
        ></span> Uhr lokale Zeit
    </div>

    <style>
        /* Dreh-Animation fürs Uhr-Icon */
        .rotate-360 {
            animation: rotate360 0.8s ease;
        }
        @keyframes rotate360 {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>


        @if ($openTime && $closeTime)
            <p class="text-gray-300">
                Von {{ $openTime->format('H:i') }} bis {{ $closeTime->format('H:i') }} Uhr
            </p>

            @if ($isOpen)
                <p class="text-green-400 mt-1 font-semibold">
                    ✅ Der Park ist aktuell geöffnet.
                </p>
            @else
                <p class="text-red-400 mt-1 font-semibold">
                    ❌ Der Park ist derzeit geschlossen.
                </p>
            @endif
        @else
            <p class="text-gray-400">
                Keine Öffnungszeiten für heute hinterlegt.
            </p>
        @endif

        @if (!$isOpen && $nextOpeningLabel)
            <div class="mt-4 text-sm text-blue-200 bg-blue-900 px-3 py-2 rounded inline-block">
                ⏭️ <span class="font-semibold text-blue-100">Nächste Öffnung:</span> {{ $nextOpeningLabel }}
            </div>
        @endif
    </div>

    <div class="mt-6">
        <span class="block text-sm font-medium text-white mb-2">Teilen:</span>
        <div class="flex flex-wrap gap-3">
            {{-- Facebook --}}
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::url()) }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#1877F2] hover:bg-[#145fc2] text-white shadow-md transition-all">
                @svg('lucide-facebook', 'w-4 h-4') Facebook
            </a>

{{-- X (Twitter) --}}
<a href="https://twitter.com/intent/tweet?text={{ urlencode($park->name) }}&url={{ urlencode(Request::url()) }}"
    target="_blank"
    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#000000] hover:bg-[#1a1a1a] text-white shadow-md transition-all">
     @svg('lucide-x', 'w-4 h-4') X
 </a>

            {{-- WhatsApp --}}
            <a href="https://wa.me/?text={{ urlencode($park->name . ' – mehr Infos hier: ' . Request::url()) }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#25D366] hover:bg-[#1ebe57] text-white shadow-md transition-all">
                @svg('lucide-message-circle', 'w-4 h-4') WhatsApp
            </a>

            {{-- Link kopieren --}}
            <button onclick="navigator.clipboard.writeText('{{ Request::url() }}')"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gray-600 hover:bg-gray-500 text-white shadow-md transition-all">
                @svg('lucide-copy', 'w-4 h-4') Link kopieren
            </button>
        </div>
    </div>

    {{-- Besucherzahlen --}}
    <div class="bg-[#1c1e5c] rounded-xl p-6 shadow-md text-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold">
                📊 Besucherzahlen
            </h3>
            <a href="{{ route('parks.statistics', $park) }}" target="_blank" rel="noopener"
               class="text-blue-300 hover:text-blue-400 transition"
               title="Externe Statistikseite öffnen">
                🌍
            </a>

            <x-park-tipps-button :slug="$park->slug" label="🎟️ Alle Besuchstipps" />
        </div>

        <div class="space-y-2 text-sm">
            {{--
            <div>
                <a href="{{ route('parks.summary', ['parkSlug' => $park->slug]) }}"
                   class="text-emerald-400 hover:underline hover:text-emerald-300 transition">
                    📄 Übersicht (Summary)
                </a>
            </div>
            --}}
            <div>
                <a href="{{ route('parks.calendar', $park) }}"
                   class="text-emerald-400 hover:underline hover:text-emerald-300 transition">
                    🗓️ Kalender (Crowd Calendar)
                </a>
            </div>
            <div>
                <a href="{{ route('parks.statistics', $park) }}"
                   class="text-emerald-400 hover:underline hover:text-emerald-300 transition">
                    📈 Statistik (Wartezeiten & Besucher)
                </a>
            </div>

            @if($visits24h > 0)
            <div
                class="inline-block bg-gradient-to-r from-red-600 to-yellow-500 text-white text-sm font-bold px-4 py-2 rounded-full shadow-lg animate__animated animate__bounceIn mb-4"
                style="box-shadow: 0 0 15px rgba(255, 100, 100, 0.6);"
            >
                🔥 {{ $visits24h }}x besucht in den letzten 24h
            </div>
            @endif
            @if($park->website_url)
            <div class="mt-2 flex flex-wrap gap-2">
                {{-- Webseite --}}
                <a href="{{ $park->website_url }}" target="_blank" class="btn btn-sm bg-indigo-600 text-white hover:bg-indigo-500 rounded-full px-3 py-1">
                    🌐 Zur offiziellen Seite
                </a>
            </div>
            @endif
        </div>
    </div>

        <!-- Bewertungen -->
        <section id="bewertungen">
            <livewire:frontend.park-bewertungen-component :park="$park" />
        </section>



        @php
        $latitude = $park->latitude;
        $longitude = $park->longitude;
        $coords = $latitude . ',' . $longitude;
        $label = urlencode($park->name);
    @endphp

    <div class="bg-[#1c1e5c] rounded-xl p-4 shadow overflow-hidden">
        <h3 class="text-xl font-semibold mb-4">📍 Karte & Anfahrt</h3>

        {{-- Eingebettete Karte mit Marker --}}
        <div class="rounded-lg overflow-hidden aspect-w-16 aspect-h-9">
            <iframe
                src="https://www.google.com/maps?q={{ $coords }}&hl=de&z=15&output=embed"
                class="w-full h-full border-0"
                loading="lazy"
                allowfullscreen>
            </iframe>
        </div>

        {{-- Button 1: Route berechnen mit Zielname --}}
        <a
            href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($park->name) }}"
            target="_blank"
            rel="noopener"
            class="block mt-4 text-center bg-yellow-400 text-black font-bold py-2 px-4 rounded hover:bg-yellow-300 transition">
            🚗 Route zu „{{ $park->name }}“ in Google Maps öffnen
        </a>
    </div>



      </div>



      </div>
    </div>
  </section>



{{-- Attraktionen --}}
@if($park->queueTimes->isNotEmpty())
<section id="wartezeiten"
    class="scroll-mt-15 py-12 bg-gradient-to-br from-purple-900 via-indigo-800 to-blue-900 text-white"
    x-data="{ status: 'all', search: '', wait: 'all' }"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <h2 class="text-3xl font-extrabold mb-6 flex items-center gap-2 drop-shadow">
            🎢 Attraktionen
        </h2>

        @if ($park->queueTimes->isEmpty())
            <div class="bg-yellow-100 text-yellow-800 text-sm px-4 py-3 rounded">
                🚫 Keine Attraktionen verfügbar.
            </div>
        @else
            <!-- Filterleiste -->
            <div class="mb-8 flex flex-col sm:flex-row gap-4 text-white">
                <!-- Status Select -->
                <div class="relative w-full sm:w-auto">
                    <span class="absolute left-3 top-2.5">🎯</span>
                    <select x-model="status"
                        class="pl-8 pr-3 py-2 rounded-md bg-white/10 text-white placeholder-white border border-white/30 backdrop-blur text-sm focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <option class="text-black" value="all">Alle Status</option>
                        <option class="text-black" value="open">🟢 Geöffnet</option>
                        <option class="text-black" value="closed">🔴 Geschlossen</option>
                    </select>
                </div>

                <!-- Wait Time Select -->
                <div class="relative w-full sm:w-auto">
                    <span class="absolute left-3 top-2.5">⏳</span>
                    <select x-model="wait"
                        class="pl-8 pr-3 py-2 rounded-md bg-white/10 text-white placeholder-white border border-white/30 backdrop-blur text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option class="text-black" value="all">Alle Wartezeiten</option>
                        <option class="text-black" value="short">⚡ Kurz (0–10)</option>
                        <option class="text-black" value="medium">⏱️ Mittel (11–30)</option>
                        <option class="text-black" value="long">⏳ Lang (31–59)</option>
                        <option class="text-black" value="verylong">🚨 Sehr lang (60+)</option>
                    </select>
                </div>

                <!-- Suche -->
                <div class="relative flex-1">
                    <span class="absolute left-3 top-2.5">🔍</span>
                    <input x-model="search"
                        type="text"
                        placeholder="Suche nach Name…"
                        class="pl-8 pr-3 py-2 rounded-md bg-white/10 text-white placeholder-white border border-white/30 backdrop-blur text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 w-full"
                    />
                </div>
            </div>

            <!-- Attraktionenliste -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <template x-for="(ride, index) in {{ $park->queueTimes->toJson() }}" :key="ride.id">
                    <div
                        x-show="(status === 'all' || status === (ride.is_open ? 'open' : 'closed'))
                            && (wait === 'all'
                                || (wait === 'short' && ride.wait_time <= 10)
                                || (wait === 'medium' && ride.wait_time > 10 && ride.wait_time <= 30)
                                || (wait === 'long' && ride.wait_time > 30 && ride.wait_time <= 59)
                                || (wait === 'verylong' && ride.wait_time >= 60))
                            && (ride.ride_name.toLowerCase().includes(search.toLowerCase()))"
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        :class="`delay-[${index * 50}ms]`"
                        class="bg-white text-gray-800 shadow-md rounded-lg overflow-hidden border-l-4 p-4"
                        :class="ride.wait_time <= 10 ? 'border-green-500' :
                                ride.wait_time <= 30 ? 'border-yellow-500' :
                                ride.wait_time <= 59 ? 'border-orange-500' : 'border-red-500'"
                    >
                        <h3 class="text-base sm:text-lg font-semibold break-words flex items-center gap-2">
                            🎠 <span x-text="ride.ride_name"></span>
                        </h3>
                        <p class="text-sm mt-2">
                            ⏱️ Wartezeit:
                            <span :class="ride.wait_time <= 10 ? 'text-green-600' :
                                          ride.wait_time <= 30 ? 'text-yellow-500' :
                                          ride.wait_time <= 59 ? 'text-orange-500' : 'text-red-600'">
                                <span x-text="ride.wait_time"></span> Minuten
                            </span><br>
                            🛠️ Status:
                            <span :class="ride.is_open ? 'text-green-600' : 'text-red-600'">
                                <span x-text="ride.is_open ? 'Geöffnet' : 'Geschlossen'"></span>
                            </span>
                        </p>
                    </div>
                </template>
            </div>
        @endif
    </div>
</section>
@endif

{{-- Besucherzahlen --}}

{{-- Nearby Parks Section --}}
{{-- Wenn Parks in der Nähe vorhanden sind --}}
{{-- Hier wird die Liste der Parks angezeigt --}}
{{-- Diese Logik könnte in einem Controller oder einer Livewire-Komponente sein --}}
{{-- $nearbyParks ist eine Collection von Parks, die in der Nähe sind --}}
{{-- Beispiel: $nearbyParks = Park::where('distance', '<=', 50)->get(); --}}
@if($nearbyParks->count())
<section class="relative mb-12 bg-center bg-cover bg-no-repeat" style="background-image: url('{{ asset('images/bg-dots-hot.png') }}');">
    <div class="max-w-7xl mx-auto px-2 sm:px-4 py-12">

        <!-- Weiße Box mit fettem Border -->
        <div class="bg-white border-4 border-pink-500 rounded-3xl shadow-xl px-4 sm:px-6 py-8">

            <h2 class="text-3xl sm:text-4xl font-extrabold mb-8 text-center
                       bg-gradient-to-r from-pink-500 to-yellow-500
                       bg-clip-text text-transparent">
                Freizeitparks in deiner Nähe
            </h2>

            <div class="swiper nearbySwiper">
                <div class="swiper-wrapper">
                    @foreach($nearbyParks as $nearby)
                    <div class="swiper-slide flex-shrink-0 w-64 sm:w-72">
                        <a href="{{ route('parks.show', $nearby->id) }}"
                           class="group block relative overflow-hidden rounded-3xl shadow-lg
                                  transform transition-transform duration-500 ease-in-out
                                  hover:scale-105 hover:shadow-2xl">

                            <!-- Verlauf-Hintergrund -->
                            <div class="absolute inset-0
                                        bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-600
                                        opacity-95 group-hover:opacity-100
                                        transition-opacity duration-500"></div>

                            <!-- Logo -->
                            <div class="relative z-10 flex justify-center pt-6 mb-4">
                                <img src="{{ asset($nearby->logo ?? 'images/logo-placeholder.png') }}"
                                     alt="Logo {{ $nearby->name }}"
                                     class="h-16 w-16 rounded-full border-4 border-white bg-white object-cover
                                            shadow-md transition-transform duration-300 group-hover:scale-110">
                            </div>

                            <!-- Bild mit Overlay -->
                            <div class="relative h-40 overflow-hidden">
                                <img loading="lazy"
                                     src="{{ asset($nearby->image ?? 'images/park-placeholder.jpg') }}"
                                     alt="{{ $nearby->name }}"
                                     class="w-full h-full object-cover
                                            transition-transform duration-500 ease-in-out
                                            group-hover:scale-110">
                                <div class="absolute inset-0
                                            bg-gradient-to-t from-black/60 to-transparent
                                            opacity-0 group-hover:opacity-100
                                            transition-opacity duration-500"></div>
                            </div>

                            <!-- Text -->
                            <div class="relative z-10 p-4 text-white">
                                <h3 class="text-lg font-bold mb-1 truncate">{{ $nearby->name }}</h3>
                                <p class="text-sm mb-4">{{ round($nearby->distance,1) }} km entfernt</p>
                                <button class="bg-pink-500 text-white font-medium px-3 py-1.5 rounded-full
                                               transition-all duration-300
                                               group-hover:bg-pink-600 group-hover:scale-105
                                               shadow-md hover:shadow-lg">
                                    Mehr erfahren
                                </button>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</section>
@endif






<!-- Karte -->
<section class="py-12 px-4 bg-[#0d0f3f]">
    <h2 class="text-2xl font-bold text-center mb-6 text-white">📍 Lage des Parks</h2>
    <div class="max-w-4xl mx-auto">
        <div class="rounded-2xl border-4 border-[#ff6600] bg-gray-900 shadow-xl overflow-hidden relative z-10">
            <div id="parkMap"
            class="w-full h-[400px] rounded-lg overflow-hidden"
            data-lat="{{ $park->latitude }}"
            data-lng="{{ $park->longitude }}"
            data-logo="{{ $park->logo ? asset($park->logo) : '' }}"
            data-name="{{ $park->name }}"
            data-location="{{ $park->location }}"
            data-country="{{ $park->country }}">
       </div>
        </div>
    </div>
</section>





  </div>



  @if($showCrowdModal)
  <div
    x-data="{ open: true }"
    x-init="window.addEventListener('bewertungGestartet', () => open = false)"
    x-show="open"
    class="fixed inset-0 z-50 flex items-center justify-center
           bg-white/10 backdrop-blur-lg transition-all duration-300"
    x-transition:enter="transition ease-out duration-300"
    x-transition:leave="transition ease-in duration-200"
    x-cloak
  >
    <div
      class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-2xl
             w-full max-w-md p-8 relative border border-white/20
             animate-fade-in"
      @click.outside="open = false"
    >
      <button @click="open = false"
        class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none">
        &times;
      </button>

      <div class="text-center">
        <h2 class="text-2xl font-bold text-[#1c1e5c] mb-2">🎢 Bist du gerade im Park?</h2>
        <p class="text-gray-600 text-sm mb-6">Hilf anderen Besuchern mit deiner Einschätzung zur aktuellen Lage im Park!</p>

        <div class="flex flex-col sm:flex-row justify-center items-center gap-3">
          <div wire:ignore>
            <livewire:frontend.park-andrang-component
                :park="$park"
                onBewertungGestartet="bewertungGestartet"
            />
          </div>

          <button
            @click="open = false; document.cookie = 'hideCrowdModal_{{ $park->id }}=1; max-age=' + (60 * 60 * 24) + '; path=/';"
            class="inline-flex justify-center items-center px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md font-medium shadow">
            Später vielleicht
          </button>
        </div>
      </div>
    </div>
  </div>
  @endif





<livewire:frontend.park-crowd-intro-component :park="$park" />


  @push('styles')
  <style>
  @keyframes shimmer {
      0% { background-position: -500px 0; }
      100% { background-position: 500px 0; }
  }

  .shimmer {
      background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.06) 50%, rgba(255,255,255,0) 100%);
      background-size: 1000px 100%;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      animation: shimmer 4s infinite linear;
  }
  </style>
  @endpush


  @endsection
