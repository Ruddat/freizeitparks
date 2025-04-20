    <!-- 🎥 VIDEO ODER BILD -->
    <div class="absolute h-[75vh] inset-0 z-0">
        @if($park->video_embed_code)
            <!-- Responsive YouTube/Vimeo Embed -->
            <iframe
                src="{{ getResponsiveYouTubeUrl($park->video_embed_code) }}"
                class="w-full h-full"
                frameborder="0"
                allow="autoplay; fullscreen"
                allowfullscreen>
            </iframe>
        @elseif($park->video_url)
            <!-- Lokales MP4-Video -->
            <video autoplay muted loop playsinline class="w-full h-full object-cover">
                <source src="{{ $park->video_url }}" type="video/mp4">
            </video>
        @else
            <!-- Fallback-Bild -->
            <img src="{{ $park->image_url ?? asset('images/fallback_park.jpg') }}"
                 alt="{{ $park->title }}"
                 class="w-full h-full object-cover brightness-90">
        @endif
    </div>

    <!-- 🏰 GROSSER PARKNAME -->
    <div class="absolute top-8 left-6 md:top-16 md:left-12 z-20 animate-fade-in-down">
        <div class="bg-white/10 backdrop-blur-md text-white px-6 py-4 rounded-xl border border-white/20 shadow-xl">
            <h1 class="text-2xl md:text-4xl font-extrabold tracking-widest uppercase drop-shadow-md">
                {{ strtoupper($park->name) }}
            </h1>
            <p class="mt-1 text-sm md:text-base font-medium opacity-80">
                {{ $park->subtitle ?? 'Spaß & Action für die ganze Familie' }}
            </p>
        </div>
    </div>

    <!-- 📦 INFOBOX -->
    <div class="relative z-10 max-w-4xl mx-auto flex justify-end pt-24 md:pt-32 px-4 md:px-6">
        <div class="bg-gradient-to-br from-pink-500 via-red-500 to-yellow-400 text-white rounded-2xl shadow-[8px_8px_0_#00000040] p-6 md:p-8 w-full sm:w-[360px] md:w-[400px] border-4 border-white/40">
            <!-- Parkname verlinkt -->
            <h2 class="text-xs font-bold text-yellow-200 mb-1">
                🎉 <a href="{{ route('parks.show', $park->id) }}" class="underline hover:text-white transition">
                    {{ $park->title }}
                </a>
            </h2>
            <p class="text-sm mb-4">
                {{ $park->hero_text ?? 'Erlebe Spaß, Action und Abenteuer für die ganze Familie!' }}
            </p>
            <a href="#infos"
               class="bg-white text-pink-600 font-bold px-4 py-2 rounded-lg shadow hover:bg-yellow-300 hover:text-black transition">
                Mehr Infos
            </a>
            <!-- Punkte Deko -->
            <div class="flex space-x-1 pt-4 justify-center">
                <span class="w-2 h-2 bg-white/70 rounded-full"></span>
                <span class="w-2 h-2 bg-white/50 rounded-full"></span>
                <span class="w-2 h-2 bg-white/30 rounded-full"></span>
            </div>
        </div>
    </div>

    {{-- 🎨 SHAPE mit echtem Farbverlauf --}}
    <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none z-10 rotate-180 animate-wave">
        <svg class="w-full h-[70px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 283.5 19.6" preserveAspectRatio="none">
            <defs>
                <linearGradient id="waveGradient" x1="0" x2="0" y1="0" y2="1">
                    <stop offset="0%" stop-color="#a855f7" />  <!-- lila -->
                    <stop offset="100%" stop-color="#ec4899" /> <!-- pink -->
                </linearGradient>
            </defs>
            <path class="elementor-shape-fill" fill="url(#waveGradient)" opacity="0.33"
            d="M0 0L0 18.8 141.8 4.1 283.5 18.8 283.5 0z"></path>
            <path class="elementor-shape-fill" fill="url(#waveGradient)" opacity="0.33"
            d="M0 0L0 12.6 141.8 4 283.5 12.6 283.5 0z"></path>
            <path class="elementor-shape-fill" fill="url(#waveGradient)" opacity="0.33"
            d="M0 0L0 6.4 141.8 4 283.5 6.4 283.5 0z"></path>
            <path class="elementor-shape-fill" fill="url(#waveGradient)"
            d="M0 0L0 1.2 141.8 4 283.5 1.2 283.5 0z"></path>
        </svg>
    </div>
