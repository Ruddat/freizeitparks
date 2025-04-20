<!-- Bewertungen -->
<div class="bg-gradient-to-br from-[#1c1e5c] to-[#0d0f3f] rounded-xl p-6 shadow-xl border border-[#2f3a8c]">
    <h3 class="text-2xl font-bold mb-4 flex items-center gap-2">
      ⭐ Besucherbewertungen
    </h3>

    <!-- Gesamtbewertung -->
    <div class="flex items-center justify-between mb-4">
      <div class="text-gray-300">Durchschnitt:</div>
      <div class="text-yellow-400 font-bold text-xl">3.6 / 5</div>
    </div>
    <div class="flex items-center space-x-1 mb-6">
      @for ($i = 1; $i <= 5; $i++)
        <svg class="w-6 h-6 {{ $i <= 4 ? 'text-yellow-400' : 'text-gray-500' }}" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 15l-5.878 3.09 1.122-6.545L.488 6.91l6.563-.955L10 0l2.949 5.955 6.563.955-4.756 4.635 1.122 6.545z"/>
        </svg>
      @endfor
    </div>

    <!-- Einzelwertungen -->
    <div class="space-y-3 text-sm text-gray-300">
      @php
        $categories = [
          '🎨 Theming' => 3,
          '🧼 Sauberkeit' => 4,
          '🍔 Gastronomie' => 3,
          '🎢 Attraktionen' => 4,
          '🤝 Service' => 5,
        ];
      @endphp

      @foreach ($categories as $label => $value)
        <div>
          <div class="flex justify-between">
            <span>{{ $label }}</span>
            <span class="text-yellow-400 font-semibold">{{ $value }}/5</span>
          </div>
          <div class="w-full bg-gray-700 h-2 rounded-full mt-1">
            <div class="h-2 rounded-full bg-yellow-400" style="width: {{ $value * 20 }}%;"></div>
          </div>
        </div>
      @endforeach
    </div>

    <!-- Kommentar -->
    <div class="mt-6 p-4 bg-[#2a2d7f] rounded-md text-gray-200 italic">
      „Sehr cooler Park, alle Leute sind total nett...“
    </div>

    <a href="#" class="block mt-4 text-sm text-blue-300 underline hover:text-blue-400 text-right">
      ➤ Alle Bewertungen anzeigen
    </a>
  </div>


  <div class="bg-[#0d0f3f] rounded-2xl p-6 shadow-xl border border-[#2f3a8c] text-white space-y-6">

    <!-- Titel -->
    <h3 class="text-3xl font-bold text-center">Bewertungen</h3>

    <!-- Durchschnitt + Kategorien -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">

      <!-- Durchschnitt -->
      <div class="bg-[#1c1e5c] rounded-xl p-4 px-6 shadow text-center">
        <div class="text-4xl text-yellow-400 font-bold flex items-center justify-center gap-2">
          ⭐ 3,6
        </div>
        <div class="text-sm text-gray-300">221 Bewertungen</div>
      </div>

      <!-- Kategorien (größer, Text unten) -->
      <div class="flex flex-wrap justify-center md:justify-start gap-6">
        @php
          $categories = [
            ['value' => '3,0', 'label' => 'Themenbereich', 'color' => '#4646e6'],
            ['value' => '3,2', 'label' => 'Sauberkeit',     'color' => '#3d77f3'],
            ['value' => '3,5', 'label' => 'Gastronomie',    'color' => '#d23ba8'],
            ['value' => '3,2', 'label' => 'Service',        'color' => '#f5c12b'],
          ];
        @endphp

        @foreach ($categories as $item)
          <div class="flex flex-col items-center space-y-1">
            <div class="w-14 h-14 rounded-full text-white flex items-center justify-center font-bold text-lg"
                 style="background-color: {{ $item['color'] }}">
              {{ $item['value'] }}
            </div>
            <div class="text-sm text-gray-200 text-center">{{ $item['label'] }}</div>
          </div>
        @endforeach
      </div>
    </div>

    <!-- Stern-Verteilung -->
    <div class="space-y-2">
      @php
        $stars = [5 => 80, 4 => 60, 3 => 40, 2 => 15, 1 => 5];
      @endphp
      @foreach($stars as $star => $percent)
        <div class="flex items-center gap-3">
          <div class="flex text-yellow-400 min-w-[80px]">
            @for($i = 0; $i < $star; $i++)
              <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.122-6.545L.488 6.91l6.563-.955L10 0l2.949 5.955 6.563.955-4.756 4.635 1.122 6.545z"/></svg>
            @endfor
          </div>
          <div class="w-full bg-gray-700 h-2 rounded-full">
            <div class="h-2 bg-yellow-400 rounded-full" style="width: {{ $percent }}%"></div>
          </div>
        </div>
      @endforeach
    </div>

    <!-- Kommentare -->
    <div class="grid md:grid-cols-3 gap-4 pt-4">
      <div class="bg-[#1c1e5c] rounded-lg p-4">
        <div class="flex gap-1 text-yellow-400 mb-1">
          @for($i = 0; $i < 5; $i++)
          <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
              <path d="M10 15l-5.878 3.09 1.122-6.545L.488 6.91l6.563-.955L10 0l2.949 5.955
                6.563.955-4.756 4.635 1.122 6.545z"/>
            </svg>
          @endfor
        </div>
        <div class="text-sm text-gray-400 mb-1">16. Apr. 2025</div>
        <div class="text-white">Sehr cooler Park, alle Leute sind total nett!</div>
      </div>
      <div class="bg-[#1c1e5c] rounded-lg p-4">
        <div class="flex gap-1 text-yellow-400 mb-1">
          @for($i = 0; $i < 3; $i++)
          <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
              <path d="M10 15l-5.878 3.09 1.122-6.545L.488 6.91l6.563-.955L10 0l2.949 5.955
                6.563.955-4.756 4.635 1.122 6.545z"/>
            </svg>
          @endfor
          @for($i = 0; $i < 2; $i++)
            <svg class="w-4 h-4 fill-current text-gray-500" viewBox="0 0 20 20">
              <path d="M10 15l-5.878 3.09 1.122-6.545L.488 6.91l6.563-.955L10 0l2.949 5.955
                6.563.955-4.756 4.635 1.122 6.545z"/>
            </svg>
          @endfor
        </div>
        <div class="text-sm text-gray-400 mb-1">16. Apr. 2025</div>
        <div class="text-white">Insgesamt eher unspektakulär</div>
      </div>
      <div class="bg-[#1c1e5c] rounded-lg p-4">
        <div class="flex gap-1 text-yellow-400 mb-1">
          @for($i = 0; $i < 4; $i++)
          <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
              <path d="M10 15l-5.878 3.09 1.122-6.545L.488 6.91l6.563-.955L10 0l2.949 5.955
                6.563.955-4.756 4.635 1.122 6.545z"/>
            </svg>
          @endfor
        </div>
        <div class="text-sm text-gray-400 mb-1">16. Apr. 2025</div>
        <div class="text-white">Tolle Shows, aber etwas überfüllt.</div>
      </div>
    </div>

    <!-- Footer Links -->
    <div class="flex justify-between items-center mt-4">
      <livewire:frontend.park-andrang-component :park="$park" />


  <!-- Wrapper mit Alpine -->
  <div x-data="{ open: false }">

      <!-- Trigger-Link -->
      <a href="#" @click.prevent="open = true" class="text-sm text-blue-300 underline hover:text-blue-400">
        ➤ Alle Bewertungen anzeigen
      </a>

      <!-- Modal -->
      <div
        x-show="open"
        x-transition
        class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50"
        style="display: none;"
      >
        <div
          class="bg-[#0d0f3f] text-white rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6 relative"
          @click.away="open = false"
        >
          <!-- Schließen -->
          <button @click="open = false" class="absolute top-3 right-3 text-gray-400 hover:text-white text-2xl">&times;</button>

          <!-- Titel -->
          <h2 class="text-2xl font-bold mb-4 text-center">Alle Bewertungen</h2>

          <!-- Bewertungsübersicht -->
          <div class="flex flex-wrap justify-between items-center bg-[#1c1e5c] p-4 rounded-lg mb-6">
            <div class="text-yellow-400 font-bold text-4xl flex items-center gap-2">⭐ 3,6</div>
            <div class="flex gap-4 mt-4 md:mt-0">
              <div class="flex flex-col items-center">
                <div class="w-12 h-12 rounded-full bg-[#4646e6] flex items-center justify-center text-white font-bold">3,0</div>
                <span class="text-xs mt-1">Themen</span>
              </div>
              <div class="flex flex-col items-center">
                <div class="w-12 h-12 rounded-full bg-[#3d77f3] flex items-center justify-center text-white font-bold">3,2</div>
                <span class="text-xs mt-1">Sauberkeit</span>
              </div>
              <div class="flex flex-col items-center">
                <div class="w-12 h-12 rounded-full bg-[#d23ba8] flex items-center justify-center text-white font-bold">3,5</div>
                <span class="text-xs mt-1">Gastro</span>
              </div>
              <div class="flex flex-col items-center">
                <div class="w-12 h-12 rounded-full bg-[#f5c12b] flex items-center justify-center text-white font-bold">3,2</div>
                <span class="text-xs mt-1">Service</span>
              </div>
            </div>
          </div>

          <!-- Bewertungen -->
          <div class="space-y-4">
            @for ($i = 0; $i < 5; $i++)
              <div class="bg-[#1c1e5c] p-4 rounded-lg">
                <div class="flex justify-between items-center mb-1">
                  <div class="flex gap-1 text-yellow-400">
                    @for ($j = 0; $j < rand(3,5); $j++)
                      <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                        <path d="M10 15l-5.878 3.09 1.122-6.545L.488 6.91l6.563-.955L10 0l2.949 5.955
                          6.563.955-4.756 4.635 1.122 6.545z" />
                      </svg>
                    @endfor
                  </div>
                  <div class="text-sm text-gray-400">User{{ $i + 1 }} · 16.04.2025</div>
                </div>
                <div class="text-gray-100">Das war ein sehr schöner Tag mit vielen tollen Attraktionen! 🍀</div>
              </div>
            @endfor
          </div>

        </div>
      </div>

    </div>
      <!-- Footer Links -->
      </div>






<header class="relative overflow-hidden">
    {{-- NAVBAR --}}
    <nav class="fixed top-0 left-0 w-full z-30 transition-all duration-500 bg-gradient-to-r from-purple-800/80 to-pink-600/80 backdrop-blur-sm shadow-xl">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center text-white">
            <a href="/" class="text-3xl font-extrabold tracking-wider neon-text">🎡 Freizeitparks</a>

            <div class="hidden md:flex space-x-6 uppercase text-sm font-semibold">
                <a href="/" class="hover:text-yellow-300 transition">Start</a>
                <a href="#park-liste" class="hover:text-yellow-300 transition">Parks</a>
                <a href="{{ route('parks.show', 1) }}" class="hover:text-yellow-300 transition">Beispielpark</a>
                <a href="#" class="hover:text-yellow-300 transition">Suche</a>
                <a href="#" class="hover:text-yellow-300 transition">Über uns</a>
            </div>

            <button id="menu-toggle" class="md:hidden focus:outline-none text-white">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16m-7 6h7"/>
                </svg>
            </button>
        </div>
        <div id="mobile-menu" class="hidden md:hidden bg-black/80 text-white px-4 pb-4 space-y-3 text-center">
            <a href="/" class="block hover:text-yellow-300">Start</a>
            <a href="#park-liste" class="block hover:text-yellow-300">Parks</a>
            <a href="{{ route('parks.show', 1) }}" class="block hover:text-yellow-300">Beispielpark</a>
            <a href="#" class="block hover:text-yellow-300">Suche</a>
            <a href="#" class="block hover:text-yellow-300">Über uns</a>
        </div>
    </nav>

    {{-- HERO-SECTION MIT VIDEO & OVERLAY --}}
    <div class="relative h-[85vh] flex items-center justify-center text-white text-center">
        <video class="absolute inset-0 w-full h-full object-cover z-0 brightness-[0.6]" autoplay muted loop playsinline>
            <source src="{{ asset('videos/rollercoaster.mp4') }}" type="video/mp4">
        </video>
        <div class="absolute inset-0 bg-gradient-to-br from-black/50 to-purple-900/60 z-10"></div>

        <div class="relative z-20 px-6 max-w-3xl space-y-6">
            <h1 class="text-5xl md:text-6xl font-black leading-tight tracking-wide neon-text animate-fade-in">Willkommen im Abenteuer!</h1>
            <p class="text-lg md:text-xl text-gray-200">Entdecke coole Freizeitparks, aktuelle Öffnungszeiten und Highlights in ganz Europa.</p>

            {{-- Suchkomponente --}}
            <livewire:frontend.parks.park-suche />

            {{-- Fancy Button --}}
            <a href="#park-liste" class="inline-block mt-6 px-6 py-3 text-white font-bold text-lg bg-gradient-to-r from-pink-500 to-purple-600 rounded-xl shadow-lg hover:scale-105 transition transform duration-300 glow-border">
                Jetzt entdecken 🚀
            </a>
        </div>

        {{-- Dekorativer Puls-Kreis --}}
        <div class="absolute bottom-12 right-12 w-16 h-16 rounded-full border-4 border-pink-500 animate-ping z-10 opacity-50"></div>
    </div>

    {{-- Untere Wellenform --}}
    <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none z-20 rotate-180 animate-wave">
        <svg class="w-full h-[80px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 283.5 19.6" preserveAspectRatio="none">
            <defs>
                <linearGradient id="waveGradient" x1="0" x2="0" y1="0" y2="1">
                    <stop offset="0%" stop-color="#a855f7" />
                    <stop offset="100%" stop-color="#ec4899" />
                </linearGradient>
            </defs>
            <path fill="url(#waveGradient)" opacity="0.5" d="M0 0L0 18.8 141.8 4.1 283.5 18.8 283.5 0z"></path>
            <path fill="url(#waveGradient)" opacity="0.4" d="M0 0L0 12.6 141.8 4 283.5 12.6 283.5 0z"></path>
            <path fill="url(#waveGradient)" opacity="0.3" d="M0 0L0 6.4 141.8 4 283.5 6.4 283.5 0z"></path>
            <path fill="url(#waveGradient)" d="M0 0L0 1.2 141.8 4 283.5 1.2 283.5 0z"></path>
        </svg>
    </div>
</header>




