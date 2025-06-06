<!DOCTYPE html>
<html lang="de" class="min-h-screen">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        @php
            $siteName = setting('site_name', 'Freizeitparks Europa');
            $siteFavicon = setting('site_favicon');
            $icon180 = setting('site_icon_180');
            $icon192 = setting('site_icon_192');
            $icon512 = setting('site_icon_512');
        @endphp

        {{-- Dynamischer SEO-Block --}}
        @include('frontend.partials.seo')

        {{-- Favicon & Icons --}}
        @if ($siteFavicon)
            <link rel="icon" type="image/webp" sizes="32x32" href="{{ asset('storage/' . $siteFavicon) }}">
        @endif

        @if ($icon180)
            <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('storage/' . $icon180) }}">
        @endif

        @if ($icon192)
            <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('storage/' . $icon192) }}">
        @endif

        @if ($icon512)
            <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('storage/' . $icon512) }}">
            <link rel="manifest" href="/manifest.json"> {{-- Optional: falls du PWA machst --}}
        @endif

        {{-- Fonts, Styles & JS --}}
        <link href="https://fonts.googleapis.com/css2?family=Bowlby+One&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="..." crossorigin="anonymous">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @stack('styles')
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="session-id" content="{{ Session::getId() }}">
    </head>
<body class="bg-white text-gray-800 min-h-screen flex flex-col">
    <header class="relative">
        @include('frontend.partials.header-nav')


        @if (Route::is('home'))
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-b from-transparent to-gray-900 opacity-50"></div>

            @include('frontend.partials.hero')
            @endif

        @if (Route::is('parks.show', 'parks.calendar'))
        {{-- 🏰 PARKDETAILS --}}

        <x-hero-park-details :park="$park" />
        @endif


        </header>

{{-- 🟡 KLICKBARER TICKET-STYLE BUTTON: PARKS ENTDECKEN
<div class="absolute top-[calc(58vh+110px)] z-40 flex justify-center w-full">
    <a href="#park-liste"
       class="bg-pink-600 text-white px-6 py-4 shadow-xl relative flex flex-col items-center justify-center rounded-xl ticket-shape hover:bg-pink-500 transition duration-300">
        <div class="flex items-center gap-2 text-xl font-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 fill-white" viewBox="0 0 24 24">
                <path d="M4 4h16v3a2 2 0 0 1 0 4v2a2 2 0 0 1 0 4v3H4v-3a2 2 0 0 1 0-4v-2a2 2 0 0 1 0-4V4zm2 2v1.17a4 4 0 0 0 0 7.66V18h12v-3.17a4 4 0 0 0 0-7.66V6H6z"/>
            </svg>
            <span>Parks entdecken</span>
        </div>
        <div class="text-sm mt-1 font-medium text-white/90">
            Jetzt loslegen & Lieblingspark finden
        </div>
    </a>
</div>
--}}
{{-- 🎨 SHAPE mit echtem Farbverlauf --}}
<div class="bottom-0 left-0 w-full overflow-hidden leading-none z-10 animate-wave">
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




    @if (session()->has('success'))
        <div class="fixed top-0 left-0 right-0 bg-green-500 text-white text-center p-4 z-50">
            {{ session('success') }}
        </div>

    @endif



    <main class="flex-grow w-full overflow-x-hidden">

        @yield('content')
    </main>



<!-- Mobile Bottom Navigation -->
<nav class="fixed bottom-0 left-0 right-0 z-50 bg-gradient-to-r from-purple-800 via-indigo-900 to-purple-800 shadow-[0_0_25px_rgba(128,90,213,0.4)] border-t border-indigo-600 md:hidden">
    <div class="flex justify-around items-center text-xs text-white font-medium">
        <!-- Home -->
        <a href="/" class="flex flex-col items-center justify-center py-3 px-2 hover:text-yellow-400 transition">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6"/>
            </svg>
            <span>Home</span>
        </a>

        <!-- Liste -->
        <a href="#park-liste" class="flex flex-col items-center justify-center py-3 px-2 hover:text-yellow-400 transition">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 5h18M3 12h18M3 19h18"/>
            </svg>
            <span>Liste</span>
        </a>

        <!-- Wartezeiten LIVE -->
        <a href="#wartezeiten" class="flex flex-col items-center justify-center py-3 px-2 text-red-400 hover:text-red-300 animate-pulse transition">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M13 16h-1v-4h-1m1-4h.01M12 20c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8z"/>
            </svg>
            <span class="tracking-wide">LIVE</span>
        </a>

        <!-- Suche -->
        <a href="/suche" class="flex flex-col items-center justify-center py-3 px-2 hover:text-yellow-400 transition">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span>Suche</span>
        </a>

        <!-- Mehr -->
        <a href="/mehr" class="flex flex-col items-center justify-center py-3 px-2 hover:text-yellow-400 transition">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 6v.01M12 12v.01M12 18v.01"/>
            </svg>
            <span>Mehr</span>
        </a>
    </div>
</nav>




    @include('frontend.partials.footer')


    <x-toast position="bottom-center" />

    @stack('scripts')

    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        menuToggle?.addEventListener('click', () => {
            mobileMenu?.classList.toggle('hidden');
        });

        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar?.classList.add('bg-gray-800', 'shadow-md');
                navbar?.classList.remove('bg-transparent');
            } else {
                navbar?.classList.add('bg-transparent');
                navbar?.classList.remove('bg-gray-800', 'shadow-md');
            }
        });

        window.addEventListener('scrollToParks', () => {
            const parkListe = document.getElementById('park-liste');
            if (parkListe) {
                parkListe.scrollIntoView({ behavior: 'smooth' });
            }
        });

        // Neues Skript für dynamische Sidebar-Positionierung
        document.addEventListener('DOMContentLoaded', () => {
            const header = document.querySelector('#navbar');
            const sidebar = document.querySelector('aside');
            const mainContent = document.querySelector('main');

            if (header) {
                const updateLayout = () => {
                    const headerHeight = header.offsetHeight;

                    if (sidebar) {
                        sidebar.style.top = `${headerHeight}px`;
                        sidebar.style.maxHeight = `calc(100vh - ${headerHeight}px)`;
                    }

                    if (mainContent) {
                        mainContent.style.paddingTop = `${headerHeight}px`;
                    }
                };

                updateLayout();
                window.addEventListener('resize', updateLayout);
            }
        });
    </script>

    <style>
        nav#navbar { z-index: 1050; }
        .mobile-menu { z-index: 1040; }
        .mobile-menu a { padding: 10px 20px; display: block; color: #fff; text-decoration: none; }

        .marker-open {
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0%   { transform: scale(1); }
            50%  { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .video-docker video {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .video-docker::after {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1;
        }

        @media (max-width: 768px) {
            .video-docker video {
                width: 100%;
                height: auto;
            }
        }
    </style>



<!-- Back to Top Button -->
<button id="backToTop"
    onclick="window.scrollTo({ top: 0, behavior: 'smooth' })"
    class="fixed bottom-6 right-6 z-50 hidden lg:flex items-center justify-center w-14 h-14 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 text-white rounded-full shadow-2xl border-2 border-white/20 backdrop-blur-md transition-all duration-300 ease-in-out opacity-0 hover:scale-110 hover:shadow-[0_0_25px_rgba(255,255,255,0.3)]">

    <!-- Icon -->
    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
    </svg>
</button>
@livewire('frontend.cookie-banner')
<!-- JavaScript für Scroll-Detektion -->
<script>
    window.addEventListener('scroll', function () {
        const btn = document.getElementById('backToTop');
        const isVisible = window.scrollY > 300;
        btn.classList.toggle('opacity-100', isVisible);
        btn.classList.toggle('opacity-0', !isVisible);
    });
</script>
</body>
</html>
