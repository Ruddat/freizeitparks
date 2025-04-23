@props(['park'])

<style>
    @keyframes floatBubble {
        0% { transform: translateY(0) scale(1); opacity: 0.5; }
        50% { transform: translateY(-30px) scale(1.15); opacity: 0.7; }
        100% { transform: translateY(0) scale(1); opacity: 0.5; }
    }
    @keyframes floatTitle {
        0% { transform: translate(-50%, -50%) translateY(0); }
        50% { transform: translate(-50%, -50%) translateY(-10px); }
        100% { transform: translate(-50%, -50%) translateY(0); }
    }
</style>

<div class="relative h-[50vh] sm:h-[60vh] w-full overflow-hidden text-white">

    {{-- 🎥 Video/Image Background --}}
    <div class="absolute inset-0 z-0 w-full h-full">
        @if($park->video_embed_code)
            <div class="absolute inset-0 w-full h-full">
                <iframe
                    src="{{ getResponsiveYouTubeUrl($park->video_embed_code) }}"
                    class="absolute inset-0 w-full h-full object-cover object-center"
                    style="min-width: 100%; min-height: 100%;"
                    loading="lazy"
                    frameborder="0"
                    allow="autoplay; fullscreen; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>
        @elseif($park->video_url)
            <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover object-center">
                <source src="{{ $park->video_url }}" type="video/mp4">
                <img src="{{ Str::startsWith($park->image, 'storage/') ? asset($park->image) : Storage::url($park->image) }}"
                     alt="{{ $park->name }}"
                     class="absolute inset-0 w-full h-full object-cover brightness-75">
            </video>
        @else
        <img src="{{ Str::startsWith($park->image, 'storage/') ? asset($park->image) : Storage::url($park->image) }}"
        alt="{{ $park->name }}"
        class="absolute inset-0 w-full h-full object-cover brightness-75"
        loading="lazy">
        @endif
    </div>

    {{-- 🔳 Overlay --}}
    <div class="absolute inset-0 bg-gradient-to-br from-black/60 to-purple-900/50 z-10"></div>

    {{-- 🏰 PERFEKT zentrierte Titelbox --}}
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-20 w-[90vw] max-w-[calc(100%-2rem)] sm:w-[85vw] md:w-[80vw] lg:max-w-3xl">
        <div class="text-center backdrop-blur-lg bg-white/10 border border-white/20 px-4 py-4 sm:px-8 sm:py-6 rounded-2xl shadow-2xl mx-auto animate-[floatTitle_3s_ease-in-out_infinite]">
            <h1 class="text-2xl xs:text-3xl sm:text-4xl md:text-5xl font-bold tracking-wide uppercase drop-shadow-lg">
                {{ strtoupper($park->name) }}
            </h1>
            <p class="mt-2 text-xs xs:text-sm sm:text-base md:text-lg font-medium opacity-90">
                {{ $park->subtitle ?? 'Spaß & Action für die ganze Familie' }}
            </p>
            <a href="#park-liste"
               class="inline-block mt-3 xs:mt-4 sm:mt-6 px-4 py-1.5 xs:px-6 xs:py-2 sm:px-8 sm:py-3 text-white font-semibold text-xs xs:text-sm sm:text-lg bg-gradient-to-r from-pink-600 to-purple-700 rounded-full shadow-xl hover:scale-105 transition-all duration-300">
                Jetzt entdecken 🚀
            </a>
        </div>
    </div>

    {{-- 🫧 Mehr Seifenblasen-Kreise (nur auf größeren Bildschirmen) --}}
    <div class="absolute inset-0 z-10 pointer-events-none md:block">
        <div class="absolute w-16 h-16 rounded-full border-2 border-pink-400/60 bg-pink-500/20 animate-[floatBubble_4s_ease-in-out_infinite] opacity-50"
             style="top: {{ rand(10, 80) }}%; left: {{ rand(5, 25) }}%; animation-delay: {{ rand(0, 3) }}s;">
        </div>
        <div class="absolute w-24 h-24 rounded-full border-2 border-purple-400/60 bg-purple-500/20 animate-[floatBubble_5s_ease-in-out_infinite] opacity-50"
             style="top: {{ rand(20, 70) }}%; left: {{ rand(60, 85) }}%; animation-delay: {{ rand(1, 4) }}s;">
        </div>
        <div class="absolute w-12 h-12 rounded-full border-2 border-pink-300/60 bg-pink-400/20 animate-[floatBubble_3s_ease-in-out_infinite] opacity-50"
             style="top: {{ rand(30, 90) }}%; left: {{ rand(15, 40) }}%; animation-delay: {{ rand(0, 2) }}s;">
        </div>
        <div class="absolute w-20 h-20 rounded-full border-2 border-purple-300/60 bg-purple-400/20 animate-[floatBubble_4.5s_ease-in-out_infinite] opacity-50"
             style="top: {{ rand(15, 85) }}%; left: {{ rand(45, 70) }}%; animation-delay: {{ rand(2, 5) }}s;">
        </div>
        <div class="absolute w-14 h-14 rounded-full border-2 border-pink-400/60 bg-pink-500/20 animate-[floatBubble_3.5s_ease-in-out_infinite] opacity-50"
             style="top: {{ rand(25, 75) }}%; left: {{ rand(30, 55) }}%; animation-delay: {{ rand(1, 3) }}s;">
        </div>
    </div>

    {{-- 🎨 SHAPE mit Farbverlauf --}}
    <div class="absolute bottom-0 left-0 w-full overflow-hidden z-10 rotate-180 animate-wave">
        <svg class="w-full h-[60px] sm:h-[80px] md:h-[100px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 283.5 19.6" preserveAspectRatio="none">
            <defs>
                <linearGradient id="waveGradient" x1="0" x2="0" y1="0" y2="1">
                    <stop offset="0%" stop-color="#a855f7" />
                    <stop offset="100%" stop-color="#ec4899" />
                </linearGradient>
            </defs>
            <path fill="url(#waveGradient)" opacity="0.4"
                  d="M0 0L0 18.8 141.8 4.1 283.5 18.8 283.5 0z"></path>
            <path fill="url(#waveGradient)" opacity="0.4"
                  d="M0 0L0 12.6 141.8 4 283.5 12.6 283.5 0z"></path>
            <path fill="url(#waveGradient)" opacity="0.4"
                  d="M0 0L0 6.4 141.8 4 283.5 6.4 283.5 0z"></path>
            <path fill="url(#waveGradient)"
                  d="M0 0L0 1.2 141.8 4 283.5 1.2 283.5 0z"></path>
        </svg>
    </div>

</div>
