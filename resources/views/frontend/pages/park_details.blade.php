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
    <section class="relative py-16 px-4 bg-gradient-to-b from-[#1c1e5c] via-[#1f236b] to-[#22286f] overflow-hidden">

        {{-- 🌥️ Linke große Deko-Wolke --}}
        <div class="absolute top-[-40px] left-[-60px] w-[260px] h-[260px] opacity-10 bg-no-repeat bg-contain pointer-events-none"
             style="background-image: url('{{ asset('icons/weather/animated/cloudy.svg') }}')"></div>

        {{-- ☀️ Rechte große Sonne --}}
        <div class="absolute bottom-[-60px] right-[-60px] w-[220px] h-[220px] opacity-25 bg-no-repeat bg-contain pointer-events-none"
             style="background-image: url('{{ asset('icons/weather/animated/clear-day.svg') }}')"></div>

        {{-- 🏰 Watermark mit Parkname und Jahr --}}
        <div class="absolute inset-0 flex justify-center items-end z-0 pointer-events-none select-none">
            <p class="text-white/5 text-[80px] md:text-[140px] font-extrabold tracking-widest uppercase leading-none text-center whitespace-nowrap relative overflow-hidden shimmer">
                {{ strtoupper($park->name) }} {{ now()->year }}
            </p>
        </div>

        {{-- 🌤️ Titel --}}
        <h2 class="text-3xl font-extrabold text-white text-center mb-10 z-10 relative flex flex-col items-center">
            <span class="text-yellow-300 text-4xl drop-shadow-lg">🌦️</span>
            <span>{{ __('messages.weather_forecast') ?? 'Wettervorhersage' }}</span>
            <span class="text-sm mt-1 text-white/70 font-light">für {{ $park->name }} · {{ now()->year }}</span>
        </h2>

        {{-- 🔮 Forecast Grid --}}
        <div class="max-w-6xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-6 text-center relative z-10">
            @foreach($forecast as $day)
                <div class="bg-[#2a2d7f] rounded-2xl p-5 shadow-xl text-white border border-white/10 hover:scale-105 transition duration-200">
                    <div class="text-lg font-semibold tracking-wide uppercase text-white/80">{{ $day['date'] }}</div>
                    <div class="my-3 h-16 flex justify-center items-center">
                        <img src="{{ $day['icon'] }}" alt="Wetter" class="h-14 w-14 drop-shadow-md">
                    </div>
                    <div class="text-red-300 font-extrabold text-2xl">{{ $day['temp_day'] }}°C</div>
                    <div class="text-sm text-gray-300">{{ __('messages.at_night') ?? 'nachts' }} {{ $day['temp_night'] }}°C</div>
                    <div class="text-sm mt-2 text-gray-200 italic">{{ $day['description'] }}</div>
                </div>
            @endforeach
        </div>

        {{-- ⬆️ Verlauf nach oben für sanften Abschluss --}}
        <div class="absolute bottom-0 left-0 w-full h-24 bg-gradient-to-t from-[#1c1e5c] to-transparent z-0 pointer-events-none"></div>

    </section>
    @endif









<!-- Info-Bereich mit Beschreibung, Bewertungen, Öffnungszeiten und Karte -->
<section class="py-12 px-4 bg-[#0d0f3f]">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-8">

      <!-- Linke Spalte: Beschreibung -->
      <div class="border border-gray-500 p-4 rounded-lg">
        <h2 class="text-2xl font-bold mb-4">Über den Freizeitpark</h2>
        <p class="text-lg text-gray-300 leading-relaxed">
          {!! $park->description ?? 'Keine Beschreibung verfügbar.' !!}
        </p>
      </div>




      <!-- Rechte Spalte: Bewertungen, Öffnungszeiten, Karte -->
      <div class="space-y-6">


        <!-- Öffnungszeiten -->
        @php
        use Carbon\Carbon;

        // Lokalisierung setzen
        Carbon::setLocale(app()->getLocale());

        // Wochentag & Zeitzone bestimmen
        $timezone = $park->timezone && in_array($park->timezone, timezone_identifiers_list())
            ? $park->timezone
            : config('app.timezone');

        $now = Carbon::now($timezone);
        $day = strtolower($now->englishDayOfWeek);       // für Datenbank-Vergleich
        $dayLabel = $now->translatedFormat('l');          // für Anzeige z. B. 'Montag' / 'Monday'

        // Öffnungszeiten des Tages
        $todayHours = $park->openingHours->firstWhere('day', $day);

        $isOpen = false;
        $openTime = null;
        $closeTime = null;

        if ($todayHours && $todayHours->open && $todayHours->close) {
            $openTime = Carbon::createFromFormat('H:i:s', $todayHours->open, $timezone);
            $closeTime = Carbon::createFromFormat('H:i:s', $todayHours->close, $timezone);
            $isOpen = $now->between($openTime, $closeTime);
        }
    @endphp

    <div class="bg-[#1c1e5c] rounded-xl p-6 shadow">
        <h3 class="text-xl font-semibold mb-2">
            🕒 {{ __('messages.opening_today') }} ({{ $dayLabel }})
        </h3>

        @if ($todayHours && $openTime && $closeTime)
            <p class="text-gray-300">
                {{ __('messages.time_range', [
                    'from' => $openTime->format('H:i'),
                    'to' => $closeTime->format('H:i'),
                ]) }}
            </p>

            @if ($isOpen)
                <p class="text-green-400 mt-1 font-semibold">
                    ✅ {{ __('messages.park_open') }}
                </p>
            @else
                <p class="text-red-400 mt-1 font-semibold">
                    ❌ {{ __('messages.park_closed') }}
                </p>
            @endif

        @else
            <p class="text-gray-400">
                {{ __('messages.no_opening_hours_today') }}
            </p>
        @endif
    </div>






        <!-- Bewertungen -->
        <livewire:frontend.park-bewertungen-component :park="$park" />


<!-- Google Maps Embed ohne API Key -->
<div class="bg-[#1c1e5c] rounded-xl p-4 shadow overflow-hidden">
    <h3 class="text-xl font-semibold mb-4">📍 Karte & Anfahrt</h3>

    <div class="rounded-lg overflow-hidden aspect-w-16 aspect-h-9">
      <iframe
        src="https://www.google.com/maps?q=Europa+Park+Rust+Germany&output=embed"
        class="w-full h-full border-0"
        loading="lazy"
        allowfullscreen>
      </iframe>
    </div>

    <a
      href="https://www.google.com/maps/dir/?api=1&destination=Europa+Park,Rust+Germany"
      target="_blank"
      rel="noopener"
      class="block mt-4 text-center bg-yellow-400 text-black font-bold py-2 px-4 rounded hover:bg-yellow-300 transition">
      🚗 Route berechnen mit Google Maps
    </a>
  </div>



      </div>
    </div>
  </section>



{{-- Attraktionen --}}
<section
    class="py-12 bg-gradient-to-br from-purple-900 via-indigo-800 to-blue-900 text-white"
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
      <h2 class="text-2xl font-bold text-center mb-6">📍 Lage des Parks</h2>
      <div class="max-w-4xl mx-auto">
        <div class="rounded-xl overflow-hidden shadow-lg">
          <!-- Beispielbild für Karte -->
          <img src="https://via.placeholder.com/800x400?text=Karte+des+Parks" alt="Karte des Parks" class="w-full h-auto" />
        </div>
      </div>
    </section>
  </div>



  @if($showCrowdModal)
  <div x-data="{ open: true }" x-show="open"
       class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60"
       x-transition:enter="transition ease-out duration-300"
       x-transition:leave="transition ease-in duration-200"
       x-cloak>
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 relative"
           @click.outside="open = false">

          <h2 class="text-xl font-semibold text-gray-800 mb-4">🎢 Bist du heute im Park?</h2>
          <p class="text-gray-600 mb-6">Du bist gerade auf der Parkseite. Möchtest du eine Bewertung abgeben?</p>

          <div class="flex flex-col sm:flex-row gap-3 justify-between">
              <a href="#bewertungsbereich"
                 class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-md font-semibold text-sm">
                 <livewire:frontend.park-andrang-component :park="$park" />
              </a>
              <button @click="open = false"
                      class="inline-flex justify-center items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md font-medium text-sm">
                  Später vielleicht
              </button>
          </div>

          <button @click="open = false"
                  class="absolute top-2 right-3 text-gray-500 hover:text-gray-700 text-xl leading-none">&times;</button>
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
