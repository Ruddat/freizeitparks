<section class="py-16 bg-[#010b3f] text-white">
    <div class="text-center mb-12">
        <h2 class="text-4xl md:text-5xl font-extrabold mb-3">
            Das richtige Wetter für deine Freizeit 🌦️
        </h2>
        <p class="text-pink-400 text-lg font-semibold tracking-wide">
            Dein 7-Tage-Ausblick – immer einen Schritt voraus!
        </p>
    </div>

    <div class="grid md:grid-cols-7 gap-6 max-w-7xl mx-auto px-4 md:px-8">
        @foreach($forecast as $day)
            <div class="bg-[#131642] border border-pink-400 rounded-xl p-4 text-white shadow-lg transition hover:scale-105 duration-300
                        flex flex-row md:flex-col justify-between items-center md:items-center text-left md:text-center gap-4 relative group">

                <!-- Tooltip Bubble -->
                <div class="absolute -top-10 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-300">
                    <div class="bg-pink-500 text-white text-xs rounded px-2 py-1 shadow-lg animate-pulse">
                        {{ ucfirst($day['description'] ?? 'Wetter') }}
                    </div>
                </div>

                <!-- Datum -->
                <div class="text-sm font-medium text-gray-300 w-24 md:w-full">
                    @php
                        try {
                            echo \Carbon\Carbon::createFromFormat('Y-m-d', $day['date'])->translatedFormat('D, d.m.');
                        } catch (Exception $e) {
                            echo $day['date']; // Fallback to original date if parsing fails
                        }
                    @endphp
                </div>

                <!-- Icon -->
                <img src="{{ $day['icon'] }}"
                     alt="Wetter Icon"
                     class="w-10 h-10 md:w-14 md:h-14 drop-shadow animate-bounce" />

                <!-- Temperatur -->
                <div class="text-sm md:text-base font-semibold text-right md:text-center">
                    <span class="bg-red-600/90 text-white px-3 py-1 rounded-full inline-block mb-1">
                        {{ $day['temp_day'] }}°C
                    </span>
                    <div class="text-blue-300 text-xs md:text-sm">
                        nachts {{ $day['temp_night'] }}°C
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
