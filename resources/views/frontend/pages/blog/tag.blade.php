@extends('frontend.layouts.app')

@section('title', $seo['title'])
@section('description', $seo['description'])

@section('canonical')
    <link rel="canonical" href="{{ url()->current() }}">
@endsection

@section('content')
<div class="bg-gradient-to-b from-blue-950 to-blue-900 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Hauptbereich -->
        <section class="lg:col-span-2">
            <h1 class="text-4xl font-extrabold mb-4 text-white">
                @isset($category)
                    Kategorie: {{ $category->name }}
                @elseif(isset($tag))
                    Schlagwort: {{ $tag->name }}
                @endif
            </h1>

            <p class="mb-8 text-blue-200">
                {{ $seo['description'] }}
            </p>

            @if($posts->count())
                @foreach($posts as $post)
                    <x-blog.card :post="$post" />
                @endforeach

                <div class="mt-10">
                    {{ $posts->links() }}
                </div>
            @else
                <p class="text-gray-300">Noch keine passenden Beiträge.</p>
            @endif
        </section>

        <!-- Sidebar -->
        <aside class="space-y-8">
            <!-- Neueste Beiträge -->
            <div class="bg-white rounded-xl shadow p-6 border border-gray-100">
                <h2 class="text-xl font-bold mb-6 text-pink-600">Neueste Beiträge</h2>
                <div class="space-y-4">
                    @foreach($latestPosts as $latest)
                        <a href="{{ route('blog.show', $latest->slug) }}" class="flex items-start gap-4 group">
                            <div class="flex-shrink-0 w-16 h-16 overflow-hidden rounded-lg shadow">
                                <img src="{{ asset($latest->featured_image) }}" class="object-cover w-full h-full group-hover:scale-105 transition duration-300" loading="lazy">
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-800 group-hover:text-pink-600">{{ $latest->title }}</h3>
                                <p class="text-xs text-gray-500 mt-1">{{ $latest->publish_start->format('d.m.Y') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Kategorien -->
            <div class="bg-white rounded-xl shadow p-6 border border-gray-100">
                <h2 class="text-xl font-bold mb-6 text-pink-600">Kategorien</h2>
                <div class="space-y-2">
                    @foreach($categories as $cat)
                        <a href="{{ route('blog.category', ['slug' => $cat->slug]) }}" class="flex justify-between text-sm hover:text-pink-600">
                            <span>{{ $cat->name }}</span>
                            <span class="text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full">{{ $cat->posts_count ?? '–' }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
