<header class="relative">
    <nav class="fixed w-full top-0 left-0 transition-all duration-300 bg-transparent" id="navbar">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="/" class="text-2xl font-bold text-white">Freizeitparks Europa</a>

            <div class="hidden md:flex space-x-8 text-white">
                <a href="/" class="hover:text-gray-300 transition">Startseite</a>
                <a href="#park-liste" class="hover:text-gray-300 transition">Freizeitparks</a>
                <a href="{{ route('parks.show', 1) }}" class="hover:text-gray-300 transition">Testpark</a>
                <a href="#" class="hover:text-gray-300 transition">Suche</a>
                <a href="#" class="hover:text-gray-300 transition">Über uns</a>
            </div>

            <button id="menu-toggle" class="md:hidden text-white focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16m-7 6h7"/>
                </svg>
            </button>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-gray-800 text-white">
            <div class="max-w-6xl mx-auto px-4 py-4 flex flex-col space-y-4">
                <a href="/" class="hover:text-gray-300 transition">Startseite</a>
                <a href="#park-liste" class="hover:text-gray-300 transition">Freizeitparks</a>
                <a href="{{ route('parks.show', 1) }}" class="hover:text-gray-300 transition">Testpark</a>
                <a href="#" class="hover:text-gray-300 transition">Suche</a>
                <a href="#" class="hover:text-gray-300 transition">Über uns</a>
            </div>
        </div>
    </nav>

    <div class="relative h-[85vh] flex flex-col justify-center items-center text-white text-center overflow-hidden">
        <video class="absolute inset-0 w-full h-full object-cover z-0" autoplay muted loop playsinline>
            <source src="{{ asset('videos/rollercoaster.mp4') }}" type="video/mp4">
            Dein Browser unterstützt dieses Video nicht.
        </video>
        <div class="absolute inset-0 bg-black/50 z-10"></div>

        <div class="relative z-20 space-y-2">
            <h1 class="text-4xl md:text-5xl font-bold">Freizeitparks in Europa entdecken</h1>
            <p class="mt-4 text-lg">Finde Freizeitparks in ganz Europa und entdecke aktuelle Öffnungszeiten, Aktionen und mehr.</p>
            <livewire:frontend.parks.park-suche />
        </div>
    </div>
</header>
