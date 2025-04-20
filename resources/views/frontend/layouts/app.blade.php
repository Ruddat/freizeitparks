<!DOCTYPE html>
<html lang="de" class="min-h-screen">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Freizeitparks Europa' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <link href="https://fonts.googleapis.com/css2?family=Bowlby+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8d7xj1z5l5e5b5e5b5e5b5e5b5e5b5e5b5e" crossorigin="anonymous">
    @livewireStyles

</head>
<body class="bg-white text-gray-800 min-h-screen flex flex-col">
    <header class="relative">
        @include('frontend.partials.header-nav')



        @if (Route::is('home'))
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-b from-transparent to-gray-900 opacity-50"></div>

            @include('frontend.partials.hero')
            @endif

        @if (Route::is('parks.show'))
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
<nav class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-300 shadow-lg md:hidden">
    <div class="flex justify-around items-center text-sm text-gray-600">
        <a href="/" class="flex flex-col items-center justify-center py-2 px-3 hover:text-yellow-500">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                 d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6"></path></svg>
            <span>Home</span>
        </a>
        <a href="#park-liste" class="flex flex-col items-center justify-center py-2 px-3 hover:text-yellow-500">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                 d="M3 5h18M3 12h18M3 19h18"></path></svg>
            <span>Liste</span>
        </a>
        <a href="#map" class="flex flex-col items-center justify-center py-2 px-3 hover:text-yellow-500">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                 d="M9 20l-5.447-2.724A2 2 0 013 15.382V5a2 2 0 012-2h14a2 2 0 012 2v10.382a2 2 0 01-1.553 1.894L15 20l-3-2-3 2z"></path></svg>
            <span>Karte</span>
        </a>
        <a href="/suche" class="flex flex-col items-center justify-center py-2 px-3 hover:text-yellow-500">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                 d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <span>Suche</span>
        </a>
        <a href="/mehr" class="flex flex-col items-center justify-center py-2 px-3 hover:text-yellow-500">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                 d="M12 6v.01M12 12v.01M12 18v.01"></path></svg>
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

</body>
</html>
