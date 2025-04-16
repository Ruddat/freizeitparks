<!DOCTYPE html>
<html lang="de" class="min-h-screen">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Freizeitparks Europa' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-white text-gray-800 min-h-screen flex flex-col">

    @include('frontend.partials.header')

    @isset($forecast)
        @if(count($forecast))
            @include('frontend.partials.forecast', ['forecast' => $forecast])
        @endif
    @endisset

    <main class="flex-grow max-w-6xl mx-auto px-4 py-12">
        @yield('content')
    </main>

    @include('frontend.partials.footer')

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
