<!DOCTYPE html>
<html lang="de" class="dark">
<head>
    <meta charset="UTF-8">
    <title>Wartungsmodus | {{ setting('site_name', 'Parkverzeichnis.de') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Wir arbeiten gerade an Verbesserungen für Parkverzeichnis.de - bald sind wir wieder für Sie da!">
    <!-- Preload wichtiger Ressourcen -->
    <link rel="preload" href="{{ asset('assets/frontend/images/maintenance/freizeitpark-bg.jpg') }}" as="image">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- FontAwesome -->
    <link rel="preconnect" href="https://kit.fontawesome.com">
    <script defer src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
    <style>
        /* Dark Mode Anpassungen */
        .dark {
            --color-bg-overlay: rgba(30, 41, 59, 0.3);
            --color-text-primary: rgba(241, 245, 249, 0.98);
            --color-text-secondary: rgba(203, 213, 225, 0.9);
            --color-card-bg: rgba(30, 41, 59, 0.6);
            --color-card-border: rgba(100, 116, 139, 0.4);
        }

        body {
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, sans-serif;
        }
        .backdrop-blur-lg {
            backdrop-filter: blur(12px);
        }

        /* SVG Animation */
        .park-icon {
            width: 200px;
            height: 200px;
            margin: 0 auto 1rem;
        }
        .ferris-wheel {
            animation: rotate 15s linear infinite;
            transform-origin: center;
        }
        .roller-coaster-car {
            animation: coaster 8s ease-in-out infinite;
        }
        @keyframes rotate {
            to { transform: rotate(360deg); }
        }
        @keyframes coaster {
            0%, 100% { transform: translateX(0) translateY(0); }
            25% { transform: translateX(10px) translateY(-5px); }
            50% { transform: translateX(0) translateY(5px); }
            75% { transform: translateX(-10px) translateY(-3px); }
        }
        .bounce {
            animation: bounce 2s ease infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body
    id="maintenanceBody"
    class="relative min-h-screen flex flex-col items-center justify-center transition-colors duration-300
           bg-cover bg-center bg-no-repeat text-gray-800 dark:text-[var(--color-text-primary)]"
    style="background-image: url('{{ asset('assets/frontend/images/maintenance/freizeitpark-bg.jpg') }}');"
    data-light-bg="{{ asset('assets/frontend/images/maintenance/freizeitpark-bg.jpg') }}"
    data-dark-bg="{{ asset('assets/frontend/images/maintenance/freizeitpark-bg-dark.jpg') }}"
>

    <!-- Overlay -->
    <div class="absolute inset-0 bg-[var(--color-bg-overlay)] backdrop-blur-md z-0"></div>

    <!-- Darkmode Toggle -->
    <div class="absolute top-4 right-4 z-10">
        <button
            id="themeToggle"
            aria-label="Dark Mode umschalten"
            class="bg-white/90 dark:bg-slate-700/80 text-gray-800 dark:text-slate-100 p-2 rounded-full shadow-lg transition hover:scale-110 focus:outline-none focus:ring-2 focus:ring-yellow-400"
        >
            <span class="dark:hidden" aria-hidden="true">🌙</span>
            <span class="hidden dark:inline" aria-hidden="true">🌞</span>
        </button>
    </div>

    <!-- Musik-Player -->
    <audio id="bgMusic" loop class="hidden">
        <source src="{{ asset('assets/audio/karussell.mp3') }}" type="audio/mpeg">
    </audio>
    <button
        id="musicToggle"
        aria-label="Hintergrundmusik steuern"
        class="absolute bottom-4 right-4 z-10 text-sm bg-white/30 dark:bg-slate-700/30 backdrop-blur px-3 py-1 rounded-full hover:scale-105 transition-transform focus:outline-none focus:ring-2 focus:ring-yellow-400 text-gray-800 dark:text-slate-200"
    >
        🎵 <span id="musicToggleText">Ton an</span>
    </button>

    <!-- Hauptinhalt -->
    <main class="relative z-10 max-w-xl w-full p-6 md:p-10 rounded-2xl shadow-xl bg-white/80 dark:bg-[var(--color-card-bg)] backdrop-blur-lg border border-white/30 dark:border-[var(--color-card-border)] text-center mx-4">
        <!-- Logo -->
        <img
            src="{{ asset('storage/' . setting('site_logo', 'assets/frontend/images/logo-neu2.png')) }}"
            alt="{{ setting('site_name', 'Parkverzeichnis.de') }}"
            class="w-32 mx-auto mb-4 rounded-xl shadow bounce"
            loading="lazy"
            width="128"
            height="128"
        >

<!-- SVG Park Animation -->
<div class="park-icon mx-auto mb-6">
    <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto max-w-[200px]">
        <!-- Riesenrad Gruppe -->
        <g class="ferris-group">
            <circle cx="100" cy="100" r="40" stroke="#ec4899" stroke-width="3" fill="none" />
            <circle cx="100" cy="60" r="6" fill="#fbbf24" />
            <circle cx="140" cy="100" r="6" fill="#fbbf24" />
            <circle cx="100" cy="140" r="6" fill="#fbbf24" />
            <circle cx="60" cy="100" r="6" fill="#fbbf24" />
        </g>
        <!-- Zentrum -->
        <circle cx="100" cy="100" r="3" fill="#f43f5e" />

        <!-- Achterbahn -->
        <path id="coasterPath" d="M20 160 Q40 120 80 140 T160 120" stroke="#3b82f6" stroke-width="2.5" fill="none" stroke-dasharray="4,2" />
        <circle r="6" fill="#ef4444">
            <animateMotion dur="5s" repeatCount="indefinite" rotate="auto">
                <mpath href="#coasterPath" />
            </animateMotion>
        </circle>

        <!-- Ballon -->
        <g class="balloon">
            <ellipse cx="180" cy="30" rx="8" ry="10" fill="#ef4444" />
            <line x1="180" y1="40" x2="180" y2="48" stroke="#10b981" stroke-width="1.5" />
            <rect x="176" y="48" width="8" height="5" fill="#10b981" rx="1" />
        </g>
    </svg>
</div>


        <h1 class="text-4xl font-bold mb-3 text-gray-900 dark:text-white drop-shadow-md">
            <span class="inline-block bounce">🎢</span> Wir drehen gerade eine Wartungsrunde <span class="inline-block bounce">🎡</span>
        </h1>

        <p class="text-lg text-gray-700 dark:text-gray-200 mb-6 leading-relaxed">
            Unser Parkverzeichnis wird gerade renoviert und für die neue Saison aufgerüstet!<br>
            Bald geht's hier wieder rund mit allen Attraktionen.
        </p>

        @if ($start_at || $end_at)
            <div class="bg-gray-100/80 dark:bg-slate-800/90 rounded-xl p-4 mb-6 border border-gray-200/50 dark:border-slate-700/50">
                <p class="text-sm text-gray-600 dark:text-slate-300 mb-1">
                    @if ($start_at)
                        <span class="block">⏱️ Geplant ab: {{ \Carbon\Carbon::parse($start_at)->format('d.m.Y H:i') }}</span>
                    @endif
                    @if ($end_at)
                        <span class="block">⏳ Voraussichtlich bis: {{ \Carbon\Carbon::parse($end_at)->format('d.m.Y H:i') }}</span>
                    @endif
                </p>

                @if ($end_at)
                    <div class="text-amber-500 dark:text-amber-300 font-semibold mt-3 text-sm" id="countdown"></div>
                @endif
            </div>
        @endif

        <a
            href="/"
            class="inline-block mt-2 px-8 py-3 bg-gradient-to-r from-rose-500 to-pink-600 hover:from-rose-600 hover:to-pink-700 text-white font-bold rounded-full transition-all shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-yellow-400 text-lg"
        >
            Zur Startseite
        </a>

        <!-- Newsletter -->
        <div class="mt-8">
            <h2 class="text-sm font-medium text-gray-300 mb-2">Bleib auf dem Laufenden</h2>
        {{-- --}}
            <livewire:frontend.marketing.newsletter-form />

        </div>

        <!-- Social Media -->
        <div class="flex justify-center gap-5 mt-8 text-2xl text-gray-600 dark:text-slate-400">
            <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="hover:text-blue-600 dark:hover:text-blue-400 transition transform hover:scale-125">
                <i class="fab fa-facebook"></i>
            </a>
            <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="hover:text-pink-600 dark:hover:text-pink-400 transition transform hover:scale-125">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="hover:text-red-600 dark:hover:text-red-400 transition transform hover:scale-125">
                <i class="fab fa-youtube"></i>
            </a>
        </div>
    </main>

    <script>
        // Musik-Steuerung
        document.addEventListener('DOMContentLoaded', () => {
            const audio = document.getElementById('bgMusic');
            const musicToggle = document.getElementById('musicToggle');
            const musicLabel = document.getElementById('musicToggleText');

            musicToggle.addEventListener('click', () => {
                if (audio.paused) {
                    audio.play().catch(e => console.error('Autoplay verhindert:', e));
                    audio.muted = false;
                    musicLabel.textContent = 'Ton aus';
                } else {
                    audio.muted = !audio.muted;
                    musicLabel.textContent = audio.muted ? 'Ton an' : 'Ton aus';
                }
            });

            // Theme Toggle
            const themeToggle = document.getElementById('themeToggle');
            themeToggle.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('darkMode', isDark);
                updateBackground();
            });

            // Initialer Theme-Zustand
            if (localStorage.getItem('darkMode')) {
                if (localStorage.getItem('darkMode') === 'true') {
                    document.documentElement.classList.add('dark');
                }
            } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.classList.add('dark');
            }
            updateBackground();

            // Countdown
            @if ($end_at)
                const endTime = new Date('{{ $end_at }}').getTime();
                const countdownElement = document.getElementById('countdown');

                function updateCountdown() {
                    const now = Date.now();
                    const distance = endTime - now;

                    if (distance <= 0) {
                        countdownElement.textContent = 'Wir sind wieder da!';
                        countdownElement.classList.add('animate-pulse', 'text-green-500', 'dark:text-green-400');
                        clearInterval(countdownInterval);
                        return;
                    }

                    const d = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

                    countdownElement.innerHTML = `
                        <span class="inline-block animate-bounce">⏳</span>
                        Noch ${d} Tag${d !== 1 ? 'e' : ''}, ${h} Stunde${h !== 1 ? 'n' : ''}, ${m} Minute${m !== 1 ? 'n' : ''}
                    `;
                }

                updateCountdown();
                const countdownInterval = setInterval(updateCountdown, 60000);
            @endif
        });

        function updateBackground() {
            const body = document.getElementById('maintenanceBody');
            const isDark = document.documentElement.classList.contains('dark');
            const bg = isDark ? body.dataset.darkBg : body.dataset.lightBg;
            body.style.backgroundImage = `url('${bg}')`;
        }
    </script>
<style>
    .ferris-group {
        animation: rotate 12s linear infinite;
        transform-origin: center;
    }
    @keyframes rotate {
        to { transform: rotate(360deg); }
    }

    .balloon {
        animation: floatBalloon 4s ease-in-out infinite;
        transform-origin: center;
    }
    @keyframes floatBalloon {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
</style>
@livewireScripts


</body>
</html>
