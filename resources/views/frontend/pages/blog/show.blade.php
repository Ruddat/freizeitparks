@extends('frontend.layouts.app')

@section('title', $post->seo_title ?? $post->title)
@section('description', $post->seo_description ?? Str::limit(strip_tags($post->excerpt), 150))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Hauptartikel -->
    <article class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-xl shadow px-6 py-8">
        <!-- Featured Image mit Lazy Loading -->
        <img src="{{ asset($post->featured_image) }}"
             alt="{{ $post->title }}"
             class="rounded-xl w-full h-80 md:h-96 object-cover mb-6 shadow-lg hover:shadow-xl transition-shadow duration-300"
             loading="lazy">

        <h1 class="text-4xl md:text-5xl font-bold mb-4 text-gray-200 dark:text-white leading-tight">
            {{ $post->title }}
        </h1>

        <!-- Meta-Informationen -->
        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500 dark:text-gray-400 mb-6">
            <span class="flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ \Carbon\Carbon::parse($post->publish_start)->translatedFormat('d. F Y') }}
            </span>
            <span>•</span>
            <span class="flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
                {{ $post->category->name }}
            </span>
            <span>•</span>
            <span class="flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                {{ number_format($post->views, 0, ',', '.') }} Aufrufe
            </span>
        </div>


        <!-- Inhalt mit verbesserter Formatierung -->
        <div class="prose dark:prose-invert prose-lg max-w-none mb-8 text-gray-800">
            @php
                // HTML aus der Datenbank bereinigen und formatieren
                $content = $post->content;

                // Entferne <html>, <head>, <body> Tags falls vorhanden
                $content = preg_replace('/<html[^>]*>|<head[^>]*>|<\/head>|<body[^>]*>|<\/body>|<\/html>/i', '', $content);


                // h2 styling
                $content = str_replace('<h2>', '<h2 class="text-2xl font-bold mt-8 mb-4 text-pink-500">', $content);

                // Ersetze <p> Tags mit Tailwind-optimierten Klassen
                $content = str_replace('<p>', '<p class="mb-6 leading-relaxed text-gray-400 dark:text-gray-300">', $content);


                    // blockquotes
                    $content = str_replace('<blockquote>', '<blockquote class="border-l-4 border-pink-400 pl-4 italic text-gray-500">', $content);



                // Emojis etwas größer darstellen
                $content = preg_replace_callback('/[\x{1F300}-\x{1F6FF}\x{1F900}-\x{1F9FF}]/u', function ($match) {
                    return '<span class="text-xl inline-block mx-0.5">'.$match[0].'</span>';
                }, $content);

                echo $content;
            @endphp
        </div>

<!-- Teilen -->
<div class="mt-10">
    <span class="block text-sm font-medium text-gray-400 dark:text-gray-300 mb-2">Diesen Beitrag teilen:</span>
    <div class="flex flex-wrap gap-2 mb-6">
        {{-- Facebook --}}
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::url()) }}"
           target="_blank"
           class="inline-flex items-center gap-2 px-4 py-1.5 text-sm rounded-full bg-[#1877F2] hover:bg-[#145fc2] text-white transition shadow">
            @svg('lucide-facebook', 'w-4 h-4') Facebook
        </a>

        {{-- X (Twitter) --}}
        <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(Request::url()) }}"
           target="_blank"
           class="inline-flex items-center gap-2 px-4 py-1.5 text-sm rounded-full bg-black hover:bg-gray-800 text-white transition shadow">
            @svg('lucide-x', 'w-4 h-4') X
        </a>

        {{-- WhatsApp --}}
        <a href="https://wa.me/?text={{ urlencode($post->title . ' – mehr Infos hier: ' . Request::url()) }}"
           target="_blank"
           class="inline-flex items-center gap-2 px-4 py-1.5 text-sm rounded-full bg-[#25D366] hover:bg-[#1ebe57] text-white transition shadow">
            @svg('lucide-message-circle', 'w-4 h-4') WhatsApp
        </a>

        {{-- Link kopieren --}}
        <button onclick="navigator.clipboard.writeText('{{ Request::url() }}')"
                class="inline-flex items-center gap-2 px-4 py-1.5 text-sm rounded-full bg-gray-600 hover:bg-gray-500 text-white transition shadow">
            @svg('lucide-copy', 'w-4 h-4') Link kopieren
        </button>
    </div>

    {{-- Tags --}}
    <div class="flex flex-wrap gap-2">
        @foreach ($post->tags as $tag)
        <a href="{{ route('blog.tag', ['slug' => $tag->slug]) }}"
               class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-pink-500/10 text-pink-400 hover:bg-pink-500/20 transition">
                #{{ $tag->name }}
            </a>
        @endforeach
    </div>
</div>


        <!-- Zurück-Button -->
        <div class="mb-12">
            <a href="{{ route('blog.index') }}"
               class="inline-flex items-center text-pink-600 dark:text-pink-400 hover:underline font-semibold transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Zurück zum Blog
            </a>
        </div>

        @if($relatedPosts->count())
        <div class="mt-16 border-t border-gray-200 dark:border-gray-700 pt-12">
            <h2 class="text-2xl font-bold text-pink-600 dark:text-pink-400 mb-8 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                Ähnliche Beiträge
            </h2>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-2">
                @foreach($relatedPosts as $related)
                    <a href="{{ route('blog.show', $related->slug) }}"
                       class="block bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-lg overflow-hidden transition transform hover:-translate-y-1 border border-gray-100 dark:border-gray-700">
                        <img src="{{ asset($related->featured_image) }}"
                             alt="{{ $related->title }}"
                             class="w-full h-48 object-cover rounded-t-xl"
                             loading="lazy">
                        <div class="p-5">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white hover:text-pink-600 dark:hover:text-pink-400 mb-2 transition">
                                {{ $related->title }}
                            </h3>
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ \Carbon\Carbon::parse($related->publish_start)->format('d.m.Y') }}
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </article>

    <!-- Sidebar -->
    <aside class="space-y-8">
        <!-- Neueste Beiträge -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-100 dark:border-gray-700">
            <h2 class="text-xl font-bold mb-6 text-pink-600 dark:text-pink-400 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Neueste Beiträge
            </h2>
            <div class="space-y-4">
                @foreach($latestPosts as $latest)
                    <a href="{{ route('blog.show', $latest->slug) }}" class="flex items-start gap-4 group">
                        <div class="flex-shrink-0 relative overflow-hidden rounded-lg w-16 h-16 shadow-sm group-hover:shadow transition">
                            <img src="{{ asset($latest->featured_image) }}"
                                 alt="{{ $latest->title }}"
                                 class="w-full h-full object-cover transform group-hover:scale-105 transition duration-300"
                                 loading="lazy">
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 group-hover:text-pink-600 dark:group-hover:text-pink-400 transition line-clamp-2">
                                {{ $latest->title }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ \Carbon\Carbon::parse($latest->publish_start)->format('d.m.Y') }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Meistgelesene Beiträge -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-100 dark:border-gray-700">
            <h2 class="text-xl font-bold mb-6 text-pink-600 dark:text-pink-400 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                Meistgelesen
            </h2>
            <div class="space-y-4">
                @foreach($popularPosts as $popular)
                    <a href="{{ route('blog.show', $popular->slug) }}"
                       class="flex justify-between items-center p-3 bg-white dark:bg-gray-700 rounded-lg shadow-sm hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                        <div class="text-sm text-gray-800 dark:text-gray-200 font-medium line-clamp-2">
                            {{ $popular->title }}
                        </div>
                        <span class="text-xs bg-pink-500 text-white px-2 py-0.5 rounded-full min-w-[40px] text-center">
                            {{ $popular->views }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Kategorien -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-100 dark:border-gray-700">
            <h2 class="text-xl font-bold mb-6 text-pink-600 dark:text-pink-400 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
                Kategorien
            </h2>
            <div class="space-y-2">
                @foreach($categories as $category)
                <a href="{{ route('blog.category', ['slug' => $category->slug]) }}"
                       class="flex items-center justify-between text-sm text-gray-700 dark:text-gray-300 hover:text-pink-600 dark:hover:text-pink-400 transition px-2 py-1 rounded hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <span>{{ $category->name }}</span>
                        <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-full">
                            {{ $category->posts_count ?? $category->posts()->count() }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </aside>
</div>
@endsection
