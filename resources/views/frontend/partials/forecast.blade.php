<section class="py-12 relative">
    {{-- Sticky Forecast Mobile --}}
    <div class="md:hidden sticky top-0 z-50 bg-gradient-to-r from-blue-600 to-indigo-700 px-2 py-1 text-white shadow-md text-xs text-center">
        Heute: {{ $forecast[0]['temp_day'] }}°C – {{ ucfirst($forecast[0]['description']) }}
    </div>

    {{-- Titelbereich --}}
    <div class="text-center mb-8 md:mb-12 px-4">
        <h2 class="text-3xl md:text-5xl font-extrabold mb-2 md:mb-3 leading-tight">
            Das richtige Wetter für deine Freizeit 🌦️
        </h2>
        <p class="text-pink-400 text-base md:text-lg font-semibold tracking-wide">
            Dein 7-Tage-Ausblick – immer einen Schritt voraus!
        </p>
    </div>

    {{-- Wetterkarten --}}
    <div class="flex md:grid md:grid-cols-7 gap-4 md:gap-6 max-w-7xl mx-auto px-4 md:px-8 overflow-x-auto md:overflow-visible scroll-smooth snap-x md:snap-none">
        @foreach($forecast as $day)
            @php
                $gradient = match ($day['weather_code']) {
                    1000 => 'from-yellow-300 via-pink-400 to-red-500',
                    1003 => 'from-gray-500 via-slate-600 to-indigo-700',
                    1063, 1183 => 'from-blue-500 via-blue-800 to-indigo-900',
                    1273 => 'from-purple-700 via-purple-900 to-black',
                    default => 'from-slate-800 to-slate-900',
                };
                $tooltip = ucfirst($day['description'] ?? 'Wetter');
            @endphp

            <div class="relative group overflow-hidden bg-gradient-to-br {{ $gradient }} border border-white/10 rounded-xl md:rounded-2xl p-3 md:p-5 text-white shadow-xl transition-transform transform hover:scale-105 duration-300 min-w-[130px] snap-start">
                {{-- Vorderseite --}}
                <div class="card-front z-10 relative space-y-2 transition-opacity duration-300 group-hover:opacity-20">
                    <div class="text-xs md:text-sm font-medium text-gray-100 text-center">
                        @php
                            try {
                                echo \Carbon\Carbon::createFromFormat('D, d.m.', $day['date'])->translatedFormat('D, d.m.');
                            } catch (Exception $e) {
                                echo $day['date'];
                            }
                        @endphp
                    </div>

                    <div class="flex justify-center">
                        <lottie-player
                            src="{{ $day['icon'] }}"
                            background="transparent"
                            speed="1"
                            style="width: 40px; height: 40px;"
                            loop
                            autoplay
                            class="drop-shadow group-hover:animate-bounce"
                        ></lottie-player>
                    </div>

                    <div class="text-center">
                        <div class="text-sm md:text-base font-semibold bg-red-600/90 px-2 py-1 rounded-full inline-block">
                            {{ $day['temp_day'] }}°C
                        </div>
                        <div class="text-blue-200 text-[10px] md:text-xs mt-1">
                            nachts {{ $day['temp_night'] }}°C
                        </div>
                    </div>
                </div>

                {{-- Hover-Overlay --}}
                <div class="card-back absolute inset-0 flex flex-col justify-center items-center text-center bg-black/80 backdrop-blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300 px-3 py-4 rounded-xl z-20">
                    <p class="text-pink-300 font-bold text-xs md:text-sm mb-1 md:mb-2">
                        {{ $tooltip }}
                    </p>
                    <ul class="space-y-[2px] md:space-y-1 text-[10px] md:text-sm text-white/90">
                        <li>💨 Wind: <strong>{{ $day['wind_speed'] ?? '?' }} km/h</strong></li>
                        <li>🌧️ Regen: <strong>{{ $day['rain_chance'] ?? '?' }}%</strong></li>
                        <li>🔆 UV-Index: <strong>{{ $day['uv_index'] ?? '?' }}</strong></li>
                    </ul>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Dark Mode Umschaltung --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const hour = new Date().getHours();
            if (hour > 18 || hour < 6) {
                document.documentElement.classList.add('dark');
            }
        });
    </script>
</section>
