<section class="max-w-7xl mx-auto px-4 py-12">
    <!-- Hero-Sektion -->
    <div class="relative bg-gradient-to-r from-pink-500 to-purple-600 rounded-2xl overflow-hidden mb-12">
        <div class="absolute inset-0 bg-black opacity-30"></div>
        <div class="relative max-w-7xl mx-auto px-8 py-20 text-center">
            <h1 class="text-5xl font-extrabold text-white mb-4 animate-bounce">🎢 Freizeitpark Entdecker</h1>
            <p class="text-xl text-white mb-8">Die besten Tipps, News und Geheimnisse aus deutschen Freizeitparks</p>
            <button class="bg-white text-pink-600 px-6 py-3 rounded-full font-bold hover:bg-gray-100 transition-all transform hover:scale-105 shadow-lg">
                Jetzt entdecken
            </button>
        </div>
    </div>

    <!-- Such- und Filterbereich -->
    <div class="bg-white p-6 rounded-2xl shadow-md mb-10">
        <div class="flex flex-col md:flex-row gap-4">
            <!-- Suche -->
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <input type="text" wire:model.live.debounce.500ms="search" class="block w-full pl-10 pr-3 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent text-amber-700" placeholder="Themen, Parks oder Stichworte suchen...">
            </div>

            <!-- Kategorien -->
            <select wire:model.live="category" class="flex-1 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent text-amber-700">
                <option value="">🌐 Alle Themen</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <!-- Sortierung -->
            <select wire:model.live="sort" class="flex-1 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent text-amber-700">
                <option value="newest">⬇️ Neueste zuerst</option>
                <option value="oldest">⬆️ Älteste zuerst</option>
                <option value="popular">🔥 Beliebteste</option>
            </select>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm mb-8">
        <div class="flex flex-wrap gap-2 items-center">
            <span class="text-sm font-medium text-gray-500 mr-2">Beliebte Tags:</span>
            @foreach($popularTags as $tag)
                <button
                    wire:click="toggleTag('{{ $tag->slug }}')"
                    class="text-xs px-3 py-1 rounded-full transition-all
                           {{ in_array($tag->slug, $selectedTags) ?
                              'bg-pink-500 text-white shadow-md' :
                              'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                    title="{{ $tag->posts_count }} Beiträge">
                    #{{ $tag->name }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Blogpost-Grid -->
    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        @forelse($posts as $post)
            <a href="{{ route('blog.show', $post->slug) }}" class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl overflow-hidden transition-all duration-300 transform hover:-translate-y-2">
                <!-- Kategorie-Badge -->
                <div class="absolute top-4 right-4 bg-pink-500 text-white text-xs font-bold px-3 py-1 rounded-full z-10">
                    {{ $post->category->name }}
                </div>

                <!-- Beitragsbild -->
                <div class="relative overflow-hidden h-60">
                    <img src="{{ asset('' . $post->featured_image) }}" alt="{{ $post->title }}"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                         loading="lazy">
                    <div class="absolute bottom-4 left-4 bg-black bg-opacity-70 text-white text-sm px-3 py-1 rounded-full">
                        ⏱️ {{ $post->reading_time }} Min.
                    </div>
                </div>

                <!-- Beitragsinhalt -->
                <div class="p-6">
                    <div class="flex items-center text-sm text-gray-500 mb-2">
                        <span>📅 {{ $post->created_at->format('d.m.Y') }}</span>
                        <span class="mx-2">•</span>
                        <span>👁️ {{ $post->views }} Aufrufe</span>
                    </div>

                    <h2 class="text-xl font-bold text-gray-800 group-hover:text-pink-600 transition mb-3 line-clamp-2">
                        {{ $post->title }}
                    </h2>

                    <p class="text-gray-600 text-sm line-clamp-3 mb-4">
                        {{ $post->excerpt }}
                    </p>

                    <div class="flex justify-between items-center">
                        <span class="text-pink-600 font-semibold flex items-center gap-1 group-hover:underline">
                            Mehr erfahren <span class="transition-transform group-hover:translate-x-1">→</span>
                        </span>
                        <div class="flex flex-wrap gap-1 max-w-xs">
                            @foreach($post->tags as $tag)
                                <span
                                    wire:click.stop="toggleTag('{{ $tag->slug }}')"
                                    class="bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-pink-600
                                           text-xs px-2 py-1 rounded-full cursor-pointer transition-all
                                           {{ in_array($tag->slug, $selectedTags) ? 'ring-2 ring-pink-400' : '' }}"
                                    title="{{ $tag->posts_count }} Beiträge">
                                    #{{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-3 text-center py-16">
                <div class="mx-auto w-24 h-24 text-gray-300 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-500 mb-2">Keine Beiträge gefunden</h3>
                <p class="text-gray-400 mb-6">Versuche es mit einem anderen Suchbegriff oder einer anderen Kategorie</p>
                <button wire:click="resetFilters" class="bg-pink-500 text-white px-6 py-2 rounded-lg hover:bg-pink-600 transition">
                    Filter zurücksetzen
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
<div class="mt-12 text-center">
    @if ($posts->hasMorePages())
        <button
            wire:click="loadMore"
            class="inline-flex items-center px-6 py-3 bg-pink-500 text-white font-bold rounded-full shadow hover:bg-pink-600 transition-all"
        >
            ➕ Mehr laden
        </button>
    @else
        <div class="text-gray-400 text-sm mt-4">
            🎉 Alle Beiträge geladen.
        </div>
    @endif
</div>

    {{-- Newsletter

    <div class="mt-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl p-8 text-white">
        <div class="max-w-3xl mx-auto text-center">
            <h3 class="text-2xl font-bold mb-2">🚀 Verpasse keine Neuigkeiten!</h3>
            <p class="mb-6">Melde dich für unseren Newsletter an und erhalte exklusive Tipps und Angebote direkt in dein Postfach.</p>

            <div class="flex flex-col sm:flex-row gap-2 max-w-md mx-auto">
                <input type="email" placeholder="Deine E-Mail-Adresse" class="flex-grow px-4 py-3 rounded-lg text-gray-800 focus:outline-none">
                <button class="bg-pink-500 hover:bg-pink-600 px-6 py-3 rounded-lg font-bold transition whitespace-nowrap">
                    Jetzt anmelden
                </button>
            </div>

            <p class="text-xs mt-3 text-blue-100">Wir geben deine Daten nicht weiter. Abmeldung jederzeit möglich.</p>
        </div>
    </div>
    --}}
</section>
