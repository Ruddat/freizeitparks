@php use Illuminate\Support\Facades\Storage; @endphp

<nav class="fixed w-full top-0 left-0 transition-all duration-300 bg-transparent z-50" id="navbar">
    <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">

        {{-- 🖼️ Logo oder Fallback --}}
        <a href="/" class="flex items-center space-x-3">
            @if (setting('site_logo'))
                <img src="{{ asset('storage/' . setting('site_logo', 'assets/frontend/images/logo-neu2.png')) }}"
                     alt="Logo" class="h-10 w-auto">
            @endif
            <span class="text-2xl font-bold text-white">Freizeitparks Europa</span>
        </a>

        {{-- 📱 Desktop Nav --}}
        <div class="hidden md:flex space-x-8 text-white">
            <a href="/" class="hover:text-gray-300 transition">Startseite</a>
            <a href="{{ route('widgets.overview') }}" class="hover:text-gray-300 transition">Widgets</a>
            @foreach($navPages as $page)
                <a href="{{ route('static.page', $page->slug) }}" class="hover:text-gray-300 transition">
                    {{ $page->title }}
                </a>
            @endforeach
        </div>

        {{-- 🍔 Toggle Button --}}
        <button id="menu-toggle" class="md:hidden text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 12h16m-7 6h7"/>
            </svg>
        </button>
    </div>

    {{-- 📱 Mobile Nav --}}
    <div id="mobile-menu" class="hidden md:hidden bg-gray-800 text-white">
        <div class="max-w-6xl mx-auto px-4 py-4 flex flex-col space-y-4">
            <a href="/" class="hover:text-gray-300 transition">Startseite</a>
            <a href="{{ route('widgets.overview') }}" class="hover:text-gray-300 transition">Widgets</a>
            @foreach($navPages as $page)
                <a href="{{ route('static.page', $page->slug) }}" class="hover:text-gray-300 transition">
                    {{ $page->title }}
                </a>
            @endforeach
        </div>
    </div>
</nav>
