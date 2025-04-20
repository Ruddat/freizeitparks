<div class="relative h-[75vh] flex flex-col justify-center items-center text-white text-center overflow-hidden">
    <video class="absolute inset-0 w-full h-full object-cover z-0" autoplay muted loop playsinline>
        <source src="{{ asset('videos/rollercoaster.mp4') }}" type="video/mp4">
    </video>
    <div class="absolute inset-0 bg-black/50 z-10"></div>

    <div class="relative z-20 space-y-2">
        <h1 class="text-4xl md:text-5xl font-bold">Freizeitparks in Europa entdecken</h1>
        <p class="mt-4 text-lg">Finde Freizeitparks in ganz Europa und entdecke aktuelle Öffnungszeiten, Aktionen und mehr.</p>
        <livewire:frontend.parks.park-suche />
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
