<div
    x-data="cookieBanner()"
    x-init="init()"
    x-cloak
    class="z-50"
>
    <!-- Cookie Banner -->
    <div
        x-show="show"
        x-transition:enter="animate-bounce-in"
        x-transition:leave="transition ease-in duration-300"
        class="fixed bottom-4 left-4 right-4 mx-auto max-w-xl p-6 bg-yellow-200 rounded-3xl shadow-2xl border-4 border-pink-400 comic-style"
    >
        <div class="flex flex-col items-center space-y-4 text-center">
            <p class="text-lg font-bold text-pink-700">
                🍪 Hey, darf's ein paar Cookies sein? Wir machen dein Erlebnis leckerer!
            </p>
            <div class="flex flex-wrap gap-4 justify-center">
                <button @click="acceptAll()" class="px-6 py-3 bg-pink-500 hover:bg-pink-600 text-white text-lg rounded-full comic-button">
                    🍪 Alles akzeptieren
                </button>
                <button @click="openSettings()" class="px-4 py-2 bg-white hover:bg-gray-100 text-pink-600 border-2 border-pink-400 rounded-full comic-button">
                    ⚙️ Einstellungen
                </button>
                <button @click="declineAll()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm rounded-full">
                    🚫 Nur notwendige
                </button>
            </div>
        </div>
    </div>

    <!-- Settings Modal -->
    <div
        x-show="settings"
        x-transition.opacity
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
    >
        <div class="bg-white p-8 rounded-2xl shadow-2xl max-w-md w-full comic-style">
            <h2 class="text-2xl text-pink-600 font-bold mb-4">Cookie Einstellungen 🍪</h2>
            <div class="space-y-4 text-left">
                <label class="flex items-center space-x-2 text-gray-700">
                    <input type="checkbox" checked disabled class="accent-pink-500">
                    <span>Essenzielle Cookies (immer aktiv)</span>
                </label>
                <label class="flex items-center space-x-2 text-gray-700">
                    <input type="checkbox" x-model="cookies.analytics" class="accent-pink-500">
                    <span>Statistik & Analyse</span>
                </label>
                <label class="flex items-center space-x-2 text-gray-700">
                    <input type="checkbox" x-model="cookies.marketing" class="accent-pink-500">
                    <span>Marketing & Werbung</span>
                </label>
            </div>
            <div class="flex justify-end mt-6 gap-4">
                <button @click="saveSettings()" class="px-4 py-2 bg-pink-500 text-white rounded-full comic-button">
                    Speichern
                </button>
                <button @click="settings = false" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-full">
                    Abbrechen
                </button>
            </div>
        </div>
    </div>

    <!-- Styles -->
    <style>
        .comic-style {
            font-family: 'Comic Neue', cursive;
        }
        .comic-button {
            font-family: 'Comic Neue', cursive;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); }
        }
        .animate-bounce-in {
            animation: bounce-in 0.6s ease-out;
        }
        [x-cloak] { display: none !important; }
    </style>

    <!-- Script -->
    <script>
        function cookieBanner() {
            return {
                show: false,
                settings: false,
                cookies: {
                    essential: true,
                    analytics: false,
                    marketing: false,
                },

                init() {
                    const consent = localStorage.getItem('cookieConsent');
                    if (!consent) {
                        this.show = true;
                    }
                },

                acceptAll() {
                    this.cookies.analytics = true;
                    this.cookies.marketing = true;
                    this.saveSettings();
                },

                declineAll() {
                    this.cookies.analytics = false;
                    this.cookies.marketing = false;
                    this.saveSettings();
                },

                openSettings() {
                    this.settings = true;
                },

                saveSettings() {
                    this.settings = false;
                    this.show = false;
                    localStorage.setItem('cookieConsent', JSON.stringify(this.cookies));
                }
            }
        }
    </script>
</div>
