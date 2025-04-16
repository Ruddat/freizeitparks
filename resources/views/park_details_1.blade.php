<!DOCTYPE html>
<html lang="de" class="min-h-screen">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>{{ $park->name ?? 'Unbekannter Park' }} - Freizeitparks Europa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-800 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="relative">
        <nav class="fixed w-full top-0 left-0 transition-all duration-300 bg-transparent z-30" id="navbar">
            <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
                <a href="/" class="text-xl sm:text-2xl font-bold text-white">Freizeitparks Europa</a>
                <div class="hidden md:flex space-x-6 text-white">
                    <a href="/" class="hover:text-gray-300 transition text-sm">Startseite</a>
                    <a href="/#park-liste" class="hover:text-gray-300 transition text-sm">Freizeitparks</a>
                    <a href="#" class="hover:text-gray-300 transition text-sm">Suche</a>
                    <a href="#" class="hover:text-gray-300 transition text-sm">Über uns</a>
                </div>
                <button id="menu-toggle" class="md:hidden text-white focus:outline-none p-2">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>
            <div id="mobile-menu" class="hidden md:hidden bg-gray-800 text-white">
                <div class="px-4 py-6 flex flex-col space-y-4">
                    <a href="/" class="hover:text-gray-300 transition text-lg py-2">Startseite</a>
                    <a href="/#park-liste" class="hover:text-gray-300 transition text-lg py-2">Freizeitparks</a>
                    <a href="#" class="hover:text-gray-300 transition text-lg py-2">Suche</a>
                    <a href="#" class="hover:text-gray-300 transition text-lg py-2">Über uns</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="flex-grow w-full max-w-full sm:max-w-3xl md:max-w-6xl sm:mx-auto px-2 sm:px-4 md:px-6 py-4 sm:py-6 md:py-12">

        <section class="relative h-64 sm:h-96 mb-8">
            <img loading="lazy" src="{{ $park->image ? asset($park->image) : asset('images/park-placeholder.jpg') }}" alt="{{ $park->name }}" class="w-full h-full object-cover rounded-lg">
            <div class="absolute inset-0 bg-black/30 flex items-end">
                <h1 class="text-2xl sm:text-4xl md:text-5xl font-bold text-white p-4 sm:p-6">{{ $park->name }}</h1>
            </div>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="md:col-span-2">
                <h2 class="text-xl sm:text-2xl font-semibold mb-4">Über den Park</h2>
                <p class="text-gray-600 text-sm sm:text-base">{{ $park->description ?? 'Keine Beschreibung verfügbar.' }}</p>
            </div>
            <div class="bg-gray-100 p-4 sm:p-6 rounded-lg">
                <h3 class="text-base sm:text-lg font-semibold mb-4">Schnellinfo</h3>
                <ul class="space-y-2 text-xs sm:text-sm text-gray-700">
                    <li><strong>Ort:</strong> {{ $park->location ?? 'Unbekannt' }}</li>
                    <li><strong>Status:</strong>
                        <span class="{{ $park->status_class }}">
                            {{ $park->status_label }}
                        </span>
                    </li>
                    <li><strong>Bewertung:</strong> {{ $park->rating ?? 'Keine' }} ({{ $park->reviews_count ?? '0' }} Bewertungen)</li>
                    @php
                    $count = $park->queueTimes->count();
                    $attrText = match(true) {
                        $count >= 50 => "$count Fahrgeschäfte – ein Tag reicht kaum!",
                        $count >= 30 => "$count Highlights voller Spaß & Action",
                        $count >= 10 => "$count Attraktionen für die ganze Familie",
                        default      => "$count Attraktionen – klein, aber fein",
                    };
                    @endphp
                    <li><strong>Erlebniswelt:</strong> {{ $attrText }}</li>
                    <li><strong>Geöffnet:</strong> {{ $park->queueTimes->where('is_open', true)->count() }} von {{ $park->queueTimes->count() }}</li>
                    <li><strong>Coolness:</strong> {{ $park->coolness ?? 'Unbekannt' }}%</li>
                </ul>
            </div>
        </section>

        <section class="mb-8">
            <h2 class="text-xl sm:text-2xl font-semibold mb-4">Öffnungszeiten</h2>
            <div class="bg-white shadow-md rounded-lg p-4 sm:p-6">
                <p class="text-sm sm:text-base">Heute geöffnet von {{ $park->opening_hours ?? 'Nicht bekannt' }}</p>
            </div>
        </section>

        <section class="mb-8" x-data="{ status: 'all', search: '', wait: 'all' }">
            <h2 class="text-xl sm:text-2xl font-semibold mb-4">Attraktionen</h2>

            @if ($park->queueTimes->isEmpty())
                <p class="text-gray-600 text-sm sm:text-base">Keine Attraktionen verfügbar.</p>
            @else
                <!-- Filterleiste -->
                <div class="mb-6 flex flex-col sm:flex-row gap-4">
                    <select x-model="status" class="px-4 py-2 border rounded text-sm w-full sm:w-auto">
                        <option value="all">Alle Status</option>
                        <option value="open">Geöffnet</option>
                        <option value="closed">Geschlossen</option>
                    </select>
                    <select x-model="wait" class="px-4 py-2 border rounded text-sm w-full sm:w-auto">
                        <option value="all">Alle Wartezeiten</option>
                        <option value="short">Kurz (0–10)</option>
                        <option value="medium">Mittel (11–30)</option>
                        <option value="long">Lang (31–59)</option>
                        <option value="verylong">Sehr lang (60+)</option>
                    </select>
                    <input x-model="search" type="text" placeholder="Suche nach Name…" class="px-4 py-2 border rounded text-sm w-full">
                </div>

                <!-- Attraktionenliste -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-4 lg:gap-6">
                    @foreach ($park->queueTimes as $ride)
                        @php
                            $wait = $ride->wait_time ?? 0;
                            $waitClass = match(true) {
                                $wait <= 10 => 'text-green-600',
                                $wait <= 30 => 'text-yellow-500',
                                $wait <= 59 => 'text-orange-500',
                                default     => 'text-red-600',
                            };
                        @endphp
                        <div
                            x-show="(status === 'all' || status === '{{ $ride->is_open ? 'open' : 'closed' }}')
                            && (wait === 'all'
                                || (wait === 'short' && {{ $wait }} <= 10)
                                || (wait === 'medium' && {{ $wait }} > 10 && {{ $wait }} <= 30)
                                || (wait === 'long' && {{ $wait }} > 30 && {{ $wait }} <= 59)
                                || (wait === 'verylong' && {{ $wait }} >= 60))
                            && (@js(strtolower($ride->ride_name)).includes(search.toLowerCase()))"
                            class="w-full box-border bg-white shadow-md rounded-lg overflow-hidden border-l-4 {{ $waitClass }}"
                        >
                            <div class="p-4">
                                <h3 class="text-base sm:text-lg font-semibold break-words">{{ $ride->ride_name }}</h3>
                                <p class="text-xs sm:text-sm text-gray-500">
                                    Wartezeit: <strong class="{{ $waitClass }}">{{ $wait }} Minuten</strong><br>
                                    Status:
                                    <span class="text-{{ $ride->is_open ? 'green' : 'red' }}-600">
                                        {{ $ride->is_open ? 'Geöffnet' : 'Geschlossen' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        @if($nearbyParks->count())
        <section class="mb-12 px-2 sm:px-4">
            <h2 class="text-xl sm:text-2xl font-semibold mb-6">Aktivitäten in der Nähe</h2>
            <div class="swiper nearbySwiper">
                <div class="swiper-wrapper">
                    @foreach($nearbyParks as $nearby)
                    <div class="swiper-slide">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden w-full max-w-xs sm:max-w-[260px]">
                            <img loading="lazy" src="{{ asset($nearby->image ?? 'images/park-placeholder.jpg') }}" class="w-full h-40 object-cover" alt="{{ $nearby->name }}">
                            <div class="p-4">
                                <h3 class="text-base sm:text-lg font-semibold">{{ $nearby->name }}</h3>
                                <p class="text-xs sm:text-sm text-gray-500">{{ round($nearby->distance, 1) }} km entfernt</p>
                                <a href="{{ route('parks.show', $nearby->id) }}" class="text-blue-600 text-sm mt-2 inline-block">Details ansehen →</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif
    </main>

    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <div>
                <h3 class="text-xl sm:text-2xl font-bold mb-4">Freizeitparks Europa</h3>
                <p class="text-gray-300 text-xs sm:text-sm">
                    Entdecke die besten Freizeitparks in Europa mit aktuellen Informationen zu Öffnungszeiten, Tickets und Aktionen.
                </p>
            </div>
            <div>
                <h4 class="text-base sm:text-lg font-semibold mb-4">Links</h4>
                <ul class="space-y-2 text-xs sm:text-sm text-gray-300">
                    <li><a href="/" class="hover:text-white transition">Startseite</a></li>
                    <li><a href="/#park-liste" class="hover:text-white transition">Freizeitparks</a></li>
                    <li><a href="#" class="hover:text-white transition">Suche</a></li>
                    <li><a href="#" class="hover:text-white transition">Über uns</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-base sm:text-lg font-semibold mb-4">Kontakt</h4>
                <ul class="space-y-2 text-xs sm:text-sm text-gray-300">
                    <li>Email: <a href="mailto:info@freizeitparks.eu" class="hover:text-white transition">info@freizeitparks.eu</a></li>
                    <li>Telefon: <a href="tel:+49123456789" class="hover:text-white transition">+49 123 456789</a></li>
                    <li>Adresse: Parkstraße 1, 12345 Freizeitstadt</li>
                </ul>
            </div>
            <div>
                <h4 class="text-base sm:text-lg font-semibold mb-4">Folge uns</h4>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-300 hover:text-white transition">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.5-4.5-10-10-10S2 6.5 2 12c0 5 3.7 9.1 8.4 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.3v7C18.3 21.1 22 17 22 12z"/></svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="mt-6 border-t border-gray-700 pt-4 text-center text-xs sm:text-sm text-gray-400">
            © 2025 Freizeitparks Europa – Mit ❤️ entwickelt
        </div>
    </footer>

    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

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
    </script>
</body>
</html>
