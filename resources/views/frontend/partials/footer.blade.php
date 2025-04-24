<footer class="bg-gradient-to-b from-gray-900 to-gray-800 text-white py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 lg:gap-12">
            <!-- Logo and Description Column -->
            <div class="md:col-span-1">
                @php
                    $footerTextsRaw = \App\Models\ModSiteSettings::where('key', 'footer_texts')->value('value');
                    $footerTexts = json_decode($footerTextsRaw ?? '[]', true);
                @endphp

                <div class="flex items-start space-x-4 mb-4 group transform transition-all duration-500 hover:-translate-y-1">
                    <a href="{{ route('home') }}" class="flex-shrink-0 transition-all duration-300 ease-in-out group-hover:rotate-3 group-hover:scale-105">
                        <img src="{{ asset('storage/' . setting('site_logo', 'assets/frontend/images/logo-neu2.png')) }}"
                             alt="{{ setting('site_name', 'Parkverzeichnis.de') }}"
                             class="h-16 w-auto drop-shadow-lg">
                    </a>

                    <div class="w-full max-w-[220px]">
                        <h3 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-yellow-400 to-yellow-200 mb-2 transition-all duration-300">
                            {{ setting('parks_intro_title', 'Freizeitparks Europa') }}
                        </h3>
                    </div>
                </div>

                <div class="max-w-[280px]">
                    <p class="text-gray-300 text-sm leading-relaxed mb-3">
                        {{ setting('parks_intro_text', 'Entdecke die besten Freizeitparks in Europa mit aktuellen Informationen zu Öffnungszeiten, Tickets und Aktionen.') }}
                    </p>

                    @if (!empty($footerTexts))
                        <div class="relative h-16 overflow-hidden">
                            <div id="rotating-footer-text"
                                 class="absolute inset-0 text-gray-200 text-sm italic transition-all duration-1000 ease-in-out"
                                 data-footer-texts='@json($footerTexts)'>
                                {{ $footerTexts[0] }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Links Column -->
            <div>
                <h4 class="text-lg font-semibold mb-4 relative inline-block">
                    <span class="relative z-10">Links</span>
                    <span class="absolute bottom-0 left-0 w-full h-1 bg-yellow-400 z-0 transform scale-x-75 origin-left"></span>
                </h4>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('home') }}" class="flex items-center text-gray-300 hover:text-white transition-all duration-300 group">
                            <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2 transform group-hover:scale-150 transition-all duration-300"></span>
                            Startseite
                        </a>
                    </li>
                    @foreach($footerPages as $page)
                        <li>
                            <a href="{{ route('static.page', $page->slug) }}" class="flex items-center text-gray-300 hover:text-white transition-all duration-300 group">
                                <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2 transform group-hover:scale-150 transition-all duration-300"></span>
                                {{ $page->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Contact Column -->
            <div>
                <h4 class="text-lg font-semibold mb-4 relative inline-block">
                    <span class="relative z-10">Kontakt</span>
                    <span class="absolute bottom-0 left-0 w-full h-1 bg-yellow-400 z-0 transform scale-x-75 origin-left"></span>
                </h4>
                <ul class="space-y-2 text-gray-300">
                    @if (setting('contact_email'))
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <a href="mailto:{{ setting('contact_email') }}" class="hover:text-white transition-all duration-300 hover:underline">{{ setting('contact_email') }}</a>
                    </li>
                    @endif
                    {{--
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <a href="tel:+49123456789" class="hover:text-white transition-all duration-300 hover:underline">+49 123 456789</a>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Parkstraße 1, 12345 Freizeitstadt</span>
                    </li>
                    --}}
                    <li>
                        <a href="https://www.paypal.com/donate?business=ingo.ruddat@gmail.com&currency_code=EUR"
                        target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-1.5 bg-yellow-500 text-[#080e3c] font-semibold rounded-full hover:bg-yellow-600 transition"
                        >
                        ❤️ Unterstützen via PayPal
                    </a>
                </li>

                </ul>
            </div>

            <!-- Social Media Column -->
            <div>
                <h4 class="text-lg font-semibold mb-4 relative inline-block">
                    <span class="relative z-10">Folge uns</span>
                    <span class="absolute bottom-0 left-0 w-full h-1 bg-yellow-400 z-0 transform scale-x-75 origin-left"></span>
                </h4>
                <div class="flex space-x-4">
                    <a href="{{ setting('facebook_url', '#') }}" class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center text-gray-300 hover:text-white hover:bg-blue-600 transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-blue-500/30">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                    <a href="{{ setting('instagram_url', '#') }}" class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center text-gray-300 hover:text-white hover:bg-gradient-to-r from-purple-500 via-pink-500 to-yellow-500 transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-pink-500/30">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                    <!-- Neues X Icon -->

                    <a href="{{ setting('x_url', '#') }}" class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center text-gray-300 hover:text-white hover:bg-black transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-gray-600/30">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z"/>
                        </svg>
                    </a>
                    <!-- Neues TikTok Icon -->
                    <a href="{{ setting('tiktok_url', '#') }}" class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center text-gray-300 hover:text-white hover:bg-black transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-gray-600/30">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                        </svg>
                    </a>
                </div>

                <!-- Newsletter Signup (optional) -->
                <div class="mt-6">
                    <h5 class="text-sm font-medium mb-2 text-gray-300">Newsletter</h5>

                    <livewire:frontend.marketing.newsletter-form />

                </div>


            </div>
        </div>

        <!-- Copyright -->
        <div class="mt-12 pt-6 border-t border-gray-700 text-center">
            <div class="text-sm text-gray-400">
                © 2025 {{ setting('site_name', 'Freizeitparks Europa') }} – Mit <span class="text-red-500">❤️</span> entwickelt
            </div>
            <div class="flex justify-center space-x-4 mt-2 text-xs text-gray-500">
                <a href="#" class="hover:text-gray-300 transition">Impressum</a>
                <span>•</span>
                <a href="{{ route('sitemap.xml') }}" class="hover:text-gray-300 transition">Sitemap</a>
                <span>•</span>
                <a href="#" class="hover:text-gray-300 transition">AGB</a>
            </div>
        </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('rotating-footer-text');
        const wrapper = document.getElementById('rotating-footer-wrapper');

        if (!el || !wrapper) return;

        const texts = JSON.parse(el.dataset.footerTexts || '[]');
        if (texts.length < 2) return;

        let index = 0;
        const height = wrapper.clientHeight;

        function rotateText() {
            index = (index + 1) % texts.length;

            const newText = document.createElement('div');
            newText.className = 'absolute inset-0 text-gray-200 text-sm italic';
            newText.textContent = texts[index];
            newText.style.top = `${height}px`;
            newText.style.opacity = '0';

            wrapper.appendChild(newText);

            // Animate
            setTimeout(() => {
                el.style.transition = 'all 0.5s ease-in-out';
                el.style.opacity = '0';
                el.style.transform = `translateY(-${height}px)`;

                newText.style.transition = 'all 0.5s ease-in-out';
                newText.style.opacity = '1';
                newText.style.top = '0';
            }, 50);

            // Clean up
            setTimeout(() => {
                wrapper.removeChild(el);
                newText.id = 'rotating-footer-text';
                newText.dataset.footerTexts = JSON.stringify(texts);
            }, 550);
        }

        setInterval(rotateText, 5000);
    });
</script>
