<footer class="bg-gray-800 text-white py-12">
    <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8">

        <div>
            @php
                $footerTextsRaw = \App\Models\ModSiteSettings::where('key', 'footer_texts')->value('value');
                $footerTexts = json_decode($footerTextsRaw ?? '[]', true);
            @endphp

            <div class="flex items-start space-x-4 mb-4 group">
                <a href="{{ route('home') }}" class="flex-shrink-0 transition-transform duration-300 ease-in-out group-hover:scale-105">
                    <img src="{{ asset('storage/' . setting('site_logo', 'assets/frontend/images/logo-neu2.png')) }}"
                         alt="{{ setting('site_name', 'Parkverzeichnis.de') }}"
                         class="h-16 w-auto">
                </a>

                <div class="w-full max-w-[220px]">
                    <h3 class="text-2xl font-bold mb-2 group-hover:text-yellow-300 transition-colors duration-300">
                        {{ setting('parks_intro_title', 'Freizeitparks Europa') }}
                    </h3>
                </div>
            </div>

            <div class="pl-[84px] max-w-[220px]">
                <p class="text-gray-300 text-sm leading-relaxed mb-3">
                    {{ setting('parks_intro_text', 'Entdecke die besten Freizeitparks in Europa mit aktuellen Informationen zu Öffnungszeiten, Tickets und Aktionen.') }}
                </p>

                @if (!empty($footerTexts))
                    <p class="text-gray-200 text-sm italic transition-opacity duration-700 ease-in-out"
                       id="rotating-footer-text"
                       data-footer-texts='@json($footerTexts)'>
                        {{ $footerTexts[0] }}
                    </p>
                @endif
            </div>
        </div>


        <div>
            <h4 class="text-lg font-semibold mb-4">Links</h4>
            <ul class="space-y-2 text-sm text-gray-300">
                <li><a href="{{ route('home') }}" class="hover:text-white transition">Startseite</a></li>
                <li><a href="#park-liste" class="hover:text-white transition">Freizeitparks</a></li>
                @foreach($footerPages as $page)
                    <li>
                        <a href="{{ route('static.page', $page->slug) }}" class="hover:text-white transition">
                            {{ $page->title }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div>
            <h4 class="text-lg font-semibold mb-4">Kontakt</h4>
            <ul class="space-y-2 text-sm text-gray-300">
                <li>Email: <a href="mailto:info@freizeitparks.eu" class="hover:text-white transition">info@freizeitparks.eu</a></li>
                <li>Telefon: <a href="tel:+49123456789" class="hover:text-white transition">+49 123 456789</a></li>
                <li>Adresse: Parkstraße 1, 12345 Freizeitstadt</li>
            </ul>
        </div>

        <div>
            <h4 class="text-lg font-semibold mb-4">Folge uns</h4>
            <div class="flex space-x-4">
                <a href="{{ setting('facebook_url', '#') }}" class="text-gray-300 hover:text-white transition">@svg('icons.fb')</a>
                <a href="{{ setting('twitter_handle', '#') }}" class="text-gray-300 hover:text-white transition">@svg('icons.twitter')</a>
                <a href="{{ setting('instagram_url', '#') }}" class="text-gray-300 hover:text-white transition">@svg('icons.instagram')</a>
            </div>
        </div>
    </div>

    <div class="mt-8 border-t border-gray-700 pt-6 text-center text-sm text-gray-400">
        © 2025 {{ setting('site_name', 'Freizeitparks Europa') }} – Mit ❤️ entwickelt
    </div>
</footer>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('rotating-footer-text');
        if (!el) return;

        const texts = JSON.parse(el.dataset.footerTexts || '[]');
        let index = 0;

        setInterval(() => {
            if (texts.length < 2) return;

            index = (index + 1) % texts.length;

            // Fade out, change text, then fade in
            el.classList.add('opacity-0');
            setTimeout(() => {
                el.textContent = texts[index];
                el.classList.remove('opacity-0');
            }, 500);
        }, 30000); // alle 30 Sekunden
    });
</script>
