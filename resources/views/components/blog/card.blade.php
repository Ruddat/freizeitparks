@props(['post'])

<article class="mb-8 bg-white dark:bg-gray-900 rounded-lg shadow p-6 border border-gray-100 dark:border-gray-800">
    <a href="{{ route('blog.show', $post->slug) }}" class="block hover:opacity-90 transition">
        <img src="{{ asset($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover rounded-md mb-4">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $post->title }}</h2>
        <p class="text-gray-600 dark:text-gray-300 mt-2 text-sm">
            {{ Str::limit($post->excerpt, 150) }}
        </p>
        <p class="mt-4 text-sm text-pink-600 dark:text-pink-400">{{ $post->publish_start->format('d.m.Y') }}</p>
    </a>
</article>
