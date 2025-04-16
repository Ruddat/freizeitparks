<!DOCTYPE html>
<html lang="de" class="min-h-screen">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Freizeitparks Europa</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="bg-white text-gray-800 min-h-screen flex flex-col">
    <!-- Hero Section -->
<!-- Header -->
<header class="relative">
    <!-- Navigation -->
    <nav class="fixed w-full top-0 left-0 transition-all duration-300 bg-transparent" id="navbar">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <!-- Logo -->
            <a href="/" class="text-2xl font-bold text-white">Freizeitparks Europa</a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex space-x-8 text-white">
                <a href="/" class="hover:text-gray-300 transition">Startseite</a>
                <a href="#park-liste" class="hover:text-gray-300 transition">Freizeitparks</a>
                <a href="{{ route('parks.show', 1) }}" class="hover:text-gray-300 transition">Testpark</a>
                <a href="#" class="hover:text-gray-300 transition">Suche</a>
                <a href="#" class="hover:text-gray-300 transition">Über uns</a>
            </div>

            <!-- Mobile Menu Button -->
            <button id="menu-toggle" class="md:hidden text-white focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
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

    <!-- Hero Section -->
    <div class="relative h-[85vh] flex flex-col justify-center items-center text-white text-center overflow-hidden">
        <!-- Video Background -->
        <video class="absolute inset-0 w-full h-full object-cover z-0" autoplay muted loop playsinline>
            <source src="{{ asset('videos/rollercoaster.mp4') }}" type="video/mp4">
            Dein Browser unterstützt dieses Video nicht.
        </video>

        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/50 z-10"></div>

        <!-- Content -->
        <div class="relative z-20 space-y-2">
            <h1 class="text-4xl md:text-5xl font-bold">Freizeitparks in Europa entdecken</h1>
            <p class="mt-4 text-lg">Finde Freizeitparks in ganz Europa und entdecke aktuelle Öffnungszeiten, Aktionen und mehr.</p>
            <livewire:frontend.parks.park-suche />
        </div>
    </div>
</header>

@if($forecast && count($forecast))
<section class="my-12">
    <h2 class="text-2xl font-semibold mb-4">Wettervorhersage (7 Tage)</h2>
    <div class="grid grid-cols-2 md:grid-cols-7 gap-4">
        @foreach($forecast as $day)
            <div class="bg-white shadow rounded-lg p-4 text-center">
                <div class="font-semibold">{{ $day['date'] }}</div>
                <img src="{{ $day['icon'] }}" alt="Icon" class="mx-auto w-12 h-12">
                <div class="mt-1 text-sm">
                    <span class="text-red-600">{{ $day['temp_day'] }}°</span> /
                    <span class="text-blue-600">{{ $day['temp_night'] }}°</span>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

    <!-- Main Content -->
    <main class="flex-grow max-w-6xl mx-auto px-4 py-12">
        <!-- Map Section -->
<!-- Map Section -->
<section class="mb-16">
    <h2 class="text-2xl font-semibold mb-6">Entdecke Parks in deiner Nähe</h2>
    <div class="w-full rounded-lg">
        <livewire:frontend.parks.park-map />
    </div>
</section>

        <!-- Park Cards -->
        <section id="park-liste">
            <h2 class="text-2xl font-semibold mb-6">Beliebte Freizeitparks</h2>
            <livewire:frontend.parks.park-liste />
        <!-- Testlink -->
    <div class="mt-6">
        <a href="{{ route('parks.show', 1) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Testlink zu Testpark 1</a>
    </div>


        </section>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Erste Karte -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <img src="{{ asset('images/park1.jpg') }}" class="w-full h-48 object-cover" alt="Park 1">
                <div class="p-4">
                    <h3 class="text-xl font-bold">Europa Park</h3>
                    <p class="text-gray-600 text-sm">Einer der größten Freizeitparks Europas mit über 100 Attraktionen.</p>
                </div>
            </div>


    </main>

<!-- Footer -->
<footer class="bg-gray-800 text-white py-12">
    <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- Logo & Beschreibung -->
        <div>
            <h3 class="text-2xl font-bold mb-4">Freizeitparks Europa</h3>
            <p class="text-gray-300 text-sm">
                Entdecke die besten Freizeitparks in Europa mit aktuellen Informationen zu Öffnungszeiten, Tickets und Aktionen.
            </p>
        </div>

        <!-- Navigation -->
        <div>
            <h4 class="text-lg font-semibold mb-4">Links</h4>
            <ul class="space-y-2 text-sm text-gray-300">
                <li><a href="#" class="hover:text-white transition">Startseite</a></li>
                <li><a href="#park-liste" class="hover:text-white transition">Freizeitparks</a></li>
                <li><a href="#" class="hover:text-white transition">Suche</a></li>
                <li><a href="#" class="hover:text-white transition">Über uns</a></li>
            </ul>
        </div>

        <!-- Kontakt -->
        <div>
            <h4 class="text-lg font-semibold mb-4">Kontakt</h4>
            <ul class="space-y-2 text-sm text-gray-300">
                <li>Email: <a href="mailto:info@freizeitparks.eu" class="hover:text-white transition">info@freizeitparks.eu</a></li>
                <li>Telefon: <a href="tel:+49123456789" class="hover:text-white transition">+49 123 456789</a></li>
                <li>Adresse: Parkstraße 1, 12345 Freizeitstadt</li>
            </ul>
        </div>

        <!-- Social Media -->
        <div>
            <h4 class="text-lg font-semibold mb-4">Folge uns</h4>
            <div class="flex space-x-4">
                <a href="#" class="text-gray-300 hover:text-white transition">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.5-4.5-10-10-10S2 6.5 2 12c0 5 3.7 9.1 8.4 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.3v7C18.3 21.1 22 17 22 12z"/></svg>
                </a>
                <a href="#" class="text-gray-300 hover:text-white transition">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.1 1.5 4.5 4.5 0 00-7.8 4.1A12.8 12.8 0 013 4.7a4.5 4.5 0 001.4 6 4.5 4.5 0 01-2-.6v.1a4.5 4.5 0 003.6 4.4 4.5 4.5 0 01-2 .1 4.5 4.5 0 004.2 3.1 9 9 0 01-6.7 1.9 12.7 12.7 0 006.9 2c8.3 0 12.8-6.9 12.8-12.8 0-.2 0-.4-.1-.6A9.1 9.1 0 0023 3z"/></svg>
                </a>
                <a href="#" class="text-gray-300 hover:text-white transition">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M7.5 2h9c3.3 0 6 2.7 6 6v9c0 3.3-2.7 6-6 6h-9c-3.3 0-6-2.7-6-6V8c0-3.3 2.7-6 6-6zm8.3 14.7c1.3-1.7 1.7-3.7 1.2-5.7-.6-2.3-2.5-4-4.8-4.5-2.3-.5-4.5.3-6 2-1.5 1.7-2 4-1.3 6.2.7 2.3 2.6 4 4.8 4.5 1.8.4 3.6-.3 4.9-2.5zm-4.8-1.7c-1.8 0-3.3-1.5-3.3-3.3s1.5-3.3 3.3-3.3 3.3 1.5 3.3 3.3-1.5 3.3-3.3 3.3z"/></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div class="mt-8 border-t border-gray-700 pt-6 text-center text-sm text-gray-400">
        © 2025 Freizeitparks Europa – Mit ❤️ entwickelt
    </div>
</footer>


</body>
</html>


<script>
    // Mobile Menu Toggle
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    menuToggle.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });

    // Navbar Background on Scroll
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('bg-gray-800', 'shadow-md');
            navbar.classList.remove('bg-transparent');
        } else {
            navbar.classList.add('bg-transparent');
            navbar.classList.remove('bg-gray-800', 'shadow-md');
        }
    });

    // Scroll to Parks
    window.addEventListener('scrollToParks', () => {
        const parkListe = document.getElementById('park-liste');
        if (parkListe) {
            parkListe.scrollIntoView({ behavior: 'smooth' });
        }
    });
</script>

<style>
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

    /* Marker Animation */
    .marker-open {
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.2);
        }
        100% {
            transform: scale(1);
        }
    }
    /* Responsive Design */
    @media (max-width: 768px) {
        .video-docker video {
            width: 100%;
            height: auto;
        }
    }
    /* Mobile Menu */




    nav#navbar {
    z-index: 1050;
}
    .mobile-menu {
        z-index: 1040;
    }
    .mobile-menu a {
        padding: 10px 20px;
        display: block;
        color: #fff;
        text-decoration: none;
    }


</style>
