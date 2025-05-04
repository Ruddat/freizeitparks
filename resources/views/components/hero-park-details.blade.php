@props(['park'])

<style>
    @keyframes floatBubble {
        0%, 100% { transform: translateY(0) scale(1); opacity: 0.4; }
        50% { transform: translateY(-20px) scale(1.1); opacity: 0.6; }
    }
    @keyframes subtleFloat {
        0%, 100% { transform: translate(-50%, -50%) translateY(0); }
        50% { transform: translate(-50%, -50%) translateY(-8px); }
    }
    .hero-button {
        background-size: 200% auto;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    .hero-button:hover {
        background-position: right center;
        transform: translateY(-2px);
        box-shadow: 0 7px 14px rgba(0,0,0,0.2);
    }
</style>

<div class="relative h-[70vh] min-h-[500px] w-full overflow-hidden text-white bg-black">

    {{-- 🎥 Background Media --}}
    <div class="absolute inset-0 z-0 w-full h-full">
        @if($park->video_embed_code)
            <div class="absolute inset-0 w-full h-full bg-black/30">
                <iframe
                    src="{{ getResponsiveYouTubeUrl($park->video_embed_code) }}?autoplay=1&mute=1&loop=1&playlist={{ $park->video_embed_code }}&controls=0&modestbranding=1"
                    class="absolute inset-0 w-full h-full object-cover opacity-90"
                    loading="eager"
                    frameborder="0"
                    allow="autoplay; fullscreen"
                    allowfullscreen>
                </iframe>
            </div>
        @elseif($park->video_url)
            <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover opacity-90">
                <source src="{{ $park->video_url }}" type="video/mp4">
                <img src="{{ $park->image_url }}" alt="{{ $park->name }}" class="absolute inset-0 w-full h-full object-cover">
            </video>
        @else
        <img
        src="{{ $park->image_url }}"
        srcset="{{ $park->image_url }} 1x, {{ $park->image_url }} 2x"
        alt="{{ $park->name }}"
        class="absolute inset-0 w-full h-full object-cover opacity-90"
        loading="lazy"
        onerror="this.onerror=null;this.src='{{ asset('images/fallback-park.jpg') }}';"
    >
        @endif
    </div>

    {{-- 🔳 Subtle Overlay --}}
    <div class="absolute inset-0 bg-gradient-to-br from-black/40 to-purple-900/30 z-10"></div>

    {{-- ✨ Subtle Floating Elements --}}
    <div class="absolute inset-0 z-20 pointer-events-none overflow-hidden">
        @for($i = 0; $i < 5; $i++)
            <div class="absolute rounded-full border border-white/20"
                 style="
                    width: {{ rand(40, 80) }}px;
                    height: {{ rand(40, 80) }}px;
                    top: {{ rand(5, 95) }}%;
                    left: {{ rand(5, 95) }}%;
                    animation: floatBubble {{ rand(5, 10) }}s ease-in-out infinite;
                    animation-delay: {{ rand(0, 5) }}s;
                 ">
            </div>
        @endfor
    </div>

    {{-- 🏰 Content Container --}}
<!-- Zentrierter Container -->
<div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-30">
    <div class="text-center bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-6 sm:p-8 max-w-4xl w-full">
        <div class="mb-6">
            <h1 class="text-4xl sm:text-6xl md:text-7xl font-bold uppercase tracking-tight text-white drop-shadow-lg">
                {{ strtoupper($park->name) }}
            </h1>
            <p class="mt-4 text-lg sm:text-xl md:text-2xl font-medium text-white/90 max-w-3xl mx-auto">
                {{ $park->subtitle ?? 'Erlebnisse, die verbinden' }}
            </p>
        </div>
        <div class="flex flex-col sm:flex-row justify-center gap-4 mt-8">
            <a href="#park-nav" class="hero-button px-8 py-3 sm:px-10 sm:py-4 text-white font-bold text-lg bg-gradient-to-r from-pink-600 to-purple-600 rounded-full shadow-md">
                Jetzt entdecken
            </a>
            <a href="{{ route('themen.park', ['slug' => $park->slug ]) }}" class="hero-button px-8 py-3 sm:px-10 sm:py-4 font-bold text-lg bg-white/10 border border-white/30 text-white rounded-full shadow-md hover:bg-white/20">
                Highlights
            </a>
        </div>
    </div>
</div>

    {{-- ⬇️ Scroll Indicator --}}
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-30 animate-bounce">
        @if($park->queueTimes->isNotEmpty())
        <a href="#wartezeiten" class="flex flex-col items-center">
            <span class="text-white/80 text-sm font-semibold">Scrollen</span>
            <svg class="w-8 h-8 text-white/80 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9 9 9-9"></path>
            </svg>
        </a>
@else
        <a href="#park-nav" class="flex flex-col items-center">
            <span class="text-white/80 text-sm font-semibold">Scrollen</span>
            <svg class="w-8 h-8 text-white/80 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9 9 9-9"></path>
            </svg>
        </a>
@endif

    </div>
    {{-- 🏰 Content Container --}}
</div>
