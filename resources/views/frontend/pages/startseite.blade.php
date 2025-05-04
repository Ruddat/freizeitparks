@extends('frontend.layouts.app')

@section('content')


    {{-- Hero-Sektion
    <section class="relative bg-cover bg-center h-[70vh] text-white" style="background-image: url('/images/hero-freizeitpark.jpg')">
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        <div class="relative z-10 max-w-4xl mx-auto px-6 py-24 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-4">Dein Abenteuer beginnt hier!</h1>
            <p class="text-lg md:text-xl mb-6">Entdecke die besten Freizeitparks in Deutschland & Europa.</p>
            <a href="#park-liste" class="bg-yellow-400 text-black px-6 py-3 rounded-lg font-semibold shadow hover:bg-yellow-300 transition">
                Jetzt entdecken
            </a>
        </div>
    </section>
 --}}


    {{-- Hero-Sektion --}}
    {{-- Park-Map --}}

    {{-- Hero-Sektion --}}


    @isset($forecast)
        @if(count($forecast))
            @include('frontend.partials.forecast', ['forecast' => $forecast])
        @endif
    @endisset





    {{-- Park-Map --}}
    <section class="relative w-screen overflow-x-hidden">
        <!-- Übergang oben als schräge Fläche -->
        <div class="absolute -top-12 left-0 w-full h-20 z-0"
             style="background: linear-gradient(to bottom, #0f172a 0%, transparent 100%); clip-path: polygon(0 0, 100% 0, 100% 100%, 0 60%);">
        </div>

        <!-- Hintergrundbild Comic-Style -->
        <div class="absolute inset-0 z-0 bg-center bg-cover"
             style="background-image:url('{{ asset('images/bg-dots-hot.png') }}'); opacity: 0.95;"></div>

        <!-- Inhalt -->
        <div class="relative z-10 max-w-screen-2xl mx-auto px-4 py-16">
            <h2 class="text-4xl font-extrabold text-center text-white drop-shadow-md mb-10">
                🎯 Finde Freizeitparks in deiner Nähe
            </h2>

            <div class="bg-white rounded-3xl border-[10px] border-yellow-400 shadow-2xl overflow-hidden">
                <livewire:frontend.parks.park-map />
            </div>
        </div>
    </section>

{{-- Park-Liste  park-liste-anchor --}}
<div id="park-liste">
    <livewire:frontend.parks.park-liste :lazy="true" />
</div>


{{--
    <div class="py-8 bg-gradient-to-r from-purple-900 to-pink-600">
        <div class="container mx-auto px-4">
          <h2 class="text-3xl md:text-4xl font-bold text-white text-center mb-6">
            Exklusive Angebote für Ihren Besuch!
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Coupon 1 -->
            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
              <h3 class="text-xl font-semibold text-purple-900 mb-2">10% Rabatt</h3>
              <p class="text-gray-600 mb-4">Auf Ihren Eintrittspreis bei Online-Buchung!</p>
              <p class="text-sm text-gray-500 mb-4">Code: THEMEPARK10</p>
              <button class="bg-pink-600 text-white font-bold py-2 px-4 rounded-full hover:bg-pink-700 transition">
                Jetzt einlösen
              </button>
            </div>
            <!-- Coupon 2 -->
            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
              <h3 class="text-xl font-semibold text-purple-900 mb-2">Familienpaket</h3>
              <p class="text-gray-600 mb-4">4 Tickets zum Preis von 3!</p>
              <p class="text-sm text-gray-500 mb-4">Code: FAMILYFUN</p>
              <button class="bg-pink-600 text-white font-bold py-2 px-4 rounded-full hover:bg-pink-700 transition">
                Jetzt einlösen
              </button>
            </div>
            <!-- Coupon 3 -->
            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
              <h3 class="text-xl font-semibold text-purple-900 mb-2">Gratis Getränk</h3>
              <p class="text-gray-600 mb-4">Bei jedem Ticketkauf vor Ort!</p>
              <p class="text-sm text-gray-500 mb-4">Code: FREEDRINK</p>
              <button class="bg-pink-600 text-white font-bold py-2 px-4 rounded-full hover:bg-pink-700 transition">
                Jetzt einlösen
              </button>
            </div>
          </div>
        </div>
      </div>

--}}
    {{-- Wettervorhersage --}}



    <style>
        .parallax {
          background-attachment: fixed;
          background-position: center;
          background-repeat: no-repeat;
          background-size: cover;
        }
      </style>
{{--
      <div class="py-12 relative">
        <!-- Parallax Hintergrund -->
        <div class="parallax absolute inset-0" style="background-image: url('https://images.unsplash.com/photo-1505751172876-fa1923c5c7a2?q=80&w=2070&auto=format&fit=crop'); opacity: 0.7;"></div>

        <!-- Inhalt -->
        <div class="relative container mx-auto px-4">
          <h2 class="text-3xl md:text-4xl font-bold text-white text-center mb-8 drop-shadow-lg">
            Die besten Themenparks weltweit
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Park 1 -->
            <div class="bg-white rounded-lg shadow-lg p-6 text-center bg-opacity-90">
              <h3 class="text-xl font-semibold text-purple-900 mb-2">Magic Adventure Park</h3>
              <p class="text-gray-600 mb-2">USA</p>
              <div class="flex justify-center mb-4">
                <span class="text-yellow-400 text-2xl">★★★★★</span>
                <span class="ml-2 text-gray-600">(4.8/5)</span>
              </div>
              <p class="text-sm text-gray-500">Theming: 5 | Sauberkeit: 4 | Gastronomie: 5 | Service: 5 | Attraktivität: 5</p>
            </div>
            <!-- Park 2 -->
            <div class="bg-white rounded-lg shadow-lg p-6 text-center bg-opacity-90">
              <h3 class="text-xl font-semibold text-purple-900 mb-2">Thrill City</h3>
              <p class="text-gray-600 mb-2">USA</p>
              <div class="flex justify-center mb-4">
                <span class="text-yellow-400 text-2xl">★★★★☆</span>
                <span class="ml-2 text-gray-600">(4.2/5)</span>
              </div>
              <p class="text-sm text-gray-500">Theming: 4 | Sauberkeit: 4 | Gastronomie: 4 | Service: 5 | Attraktivität: 4</p>
            </div>
            <!-- Park 3 -->
            <div class="bg-white rounded-lg shadow-lg p-6 text-center bg-opacity-90">
              <h3 class="text-xl font-semibold text-purple-900 mb-2">Fantasy Land</h3>
              <p class="text-gray-600 mb-2">USA</p>
              <div class="flex justify-center mb-4">
                <span class="text-yellow-400 text-2xl">★★★★☆</span>
                <span class="ml-2 text-gray-600">(4.0/5)</span>
              </div>
              <p class="text-sm text-gray-500">Theming: 4 | Sauberkeit: 3 | Gastronomie: 4 | Service: 4 | Attraktivität: 5</p>
            </div>
          </div>
        </div>
      </div>



      <div class="py-12 bg-gradient-to-r from-purple-900 to-pink-600">
        <div class="container mx-auto px-4">
          <h2 class="text-3xl md:text-4xl font-bold text-white text-center mb-8">
            Entdecken Sie unser Angebot
          </h2>

          <!-- Filter Buttons -->
          <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
            <button onclick="showContent('gastronomie')" class="bg-blue-500 text-white font-semibold py-4 px-6 rounded-lg hover:bg-blue-600 transition flex items-center justify-center">
              <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M3 12h18m-7 6h7"></path></svg>
              Gastronomie
            </button>
            <button onclick="showContent('attraktionen')" class="bg-purple-500 text-white font-semibold py-4 px-6 rounded-lg hover:bg-purple-600 transition flex items-center justify-center">
              <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
              Attraktionen
            </button>
            <button onclick="showContent('aktivitaeten')" class="bg-green-500 text-white font-semibold py-4 px-6 rounded-lg hover:bg-green-600 transition flex items-center justify-center">
              <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              Aktivitäten
            </button>
            <button onclick="showContent('familienparks')" class="bg-red-500 text-white font-semibold py-4 px-6 rounded-lg hover:bg-red-600 transition flex items-center justify-center">
              <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5c2.8 0 5.4 1.8 6.3 4.5H12v3h6.3c-.9 2.7-3.5 4.5-6.3 4.5-3.7 0-6.7-3-6.7-6.7s3-6.7 6.7-6.7z"></path></svg>
              Familienparks
            </button>
            <button onclick="showContent('natur')" class="bg-yellow-500 text-white font-semibold py-4 px-6 rounded-lg hover:bg-yellow-600 transition flex items-center justify-center">
              <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v6m0 0v6m0-6h6m-6 0H6"></path></svg>
              Natur & Erholung
            </button>
          </div>

          <!-- Inhalte -->
          <div id="content-gastronomie" class="content hidden bg-white bg-opacity-90 rounded-lg p-6">
            <h3 class="text-2xl font-semibold text-purple-900 mb-4">Gastronomie</h3>
            <p class="text-gray-600">Genießen Sie eine Vielzahl an Restaurants und Snackbars – von Burger bis Pizza, für jeden Geschmack etwas dabei!</p>
            <ul class="list-disc list-inside mt-4 text-gray-600">
              <li>Burger Bonanza – Saftige Burger direkt neben der Achterbahn</li>
              <li>Pizza Plaza – Frische Pizzen mit Blick auf den Wasserpark</li>
              <li>Eisstand – Perfekt für eine Abkühlung an heißen Tagen</li>
            </ul>
          </div>

          <div id="content-attraktionen" class="content hidden bg-white bg-opacity-90 rounded-lg p-6">
            <h3 class="text-2xl font-semibold text-purple-900 mb-4">Attraktionen</h3>
            <p class="text-gray-600">Erleben Sie Nervenkitzel und Spaß mit unseren Top-Attraktionen!</p>
            <ul class="list-disc list-inside mt-4 text-gray-600">
              <li>Pharaohs Fury – Eine Achterbahn mit 360°-Loopings</li>
              <li>Sky Tower – Der höchste Free-Fall-Turm Europas</li>
              <li>Kinderland – Karussells und Spiele für die Kleinen</li>
            </ul>
          </div>

          <div id="content-aktivitaeten" class="content hidden bg-white bg-opacity-90 rounded-lg p-6">
            <h3 class="text-2xl font-semibold text-purple-900 mb-4">Aktivitäten</h3>
            <p class="text-gray-600">Von Abenteuer bis Entspannung – hier ist für jeden etwas dabei!</p>
            <ul class="list-disc list-inside mt-4 text-gray-600">
              <li>Kletterpark – Für mutige Abenteurer</li>
              <li>Mini-Golf – Spaß für die ganze Familie</li>
              <li>Zoo-Besuch – Tiere hautnah erleben</li>
            </ul>
          </div>

          <div id="content-familienparks" class="content hidden bg-white bg-opacity-90 rounded-lg p-6">
            <h3 class="text-2xl font-semibold text-purple-900 mb-4">Familienparks</h3>
            <p class="text-gray-600">Erlebnisse für Groß und Klein – perfekt für einen Familienausflug!</p>
            <ul class="list-disc list-inside mt-4 text-gray-600">
              <li>Familienachterbahn – Spaß für alle Altersgruppen</li>
              <li>Spielplatz – Mit Rutschen und Schaukeln</li>
              <li>Puppentheater – Unterhaltung für die Kleinen</li>
            </ul>
          </div>

          <div id="content-natur" class="content hidden bg-white bg-opacity-90 rounded-lg p-6">
            <h3 class="text-2xl font-semibold text-purple-900 mb-4">Natur & Erholung</h3>
            <p class="text-gray-600">Entspannen Sie in grüner Umgebung – ideal für ein Picknick oder einen Spaziergang.</p>
            <ul class="list-disc list-inside mt-4 text-gray-600">
              <li>Botanischer Garten – Wunderschöne Pflanzenwelt</li>
              <li>Picknickwiese – Mit Blick auf den See</li>
              <li>Wanderwege – Natur pur erleben</li>
            </ul>
          </div>
        </div>
      </div>

      <script>
        function showContent(section) {
          // Alle Inhalte ausblenden
          document.querySelectorAll('.content').forEach(content => {
            content.classList.add('hidden');
          });

          // Gewählten Inhalt anzeigen
          document.getElementById(`content-${section}`).classList.remove('hidden');
        }

        // Standardmäßig den ersten Tab anzeigen
        showContent('gastronomie');
      </script>

--}}


    {{-- Vorteile / Kategorien
    <section class="bg-yellow-100 py-12">
        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div>
                <img src="/images/icon-family.svg" class="mx-auto h-16 mb-4" alt="Familienparks">
                <h3 class="text-xl font-semibold mb-2">Für die ganze Familie</h3>
                <p class="text-gray-700">Erlebnisse für Groß und Klein – von Kinderkarussell bis Achterbahn!</p>
            </div>
            <div>
                <img src="/images/icon-thrill.svg" class="mx-auto h-16 mb-4" alt="Adrenalin pur">
                <h3 class="text-xl font-semibold mb-2">Adrenalin garantiert</h3>
                <p class="text-gray-700">Entdecke die krassesten Fahrgeschäfte Europas!</p>
            </div>
            <div>
                <img src="/images/icon-nature.svg" class="mx-auto h-16 mb-4" alt="Parks im Grünen">
                <h3 class="text-xl font-semibold mb-2">Parks im Grünen</h3>
                <p class="text-gray-700">Freizeit mit Natur pur – ideal für Picknick und Erholung.</p>
            </div>
        </div>
    </section>
--}}
@endsection
