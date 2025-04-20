@props([
    'rating' => 0,
    'type' => null, // z. B. "service", "cleanliness", etc.
])

@php
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;

    // Farben pro Typ
    $colorMap = [
        'crowd' => 'bg-blue-500 text-white',
        'attractiveness' => 'bg-yellow-400 text-black',
        'theming' => 'bg-purple-400 text-white',
        'gastronomy' => 'bg-orange-400 text-white',
        'cleanliness' => 'bg-green-500 text-white',
        'service' => 'bg-blue-500 text-white',
        null => 'bg-yellow-400 text-black', // fallback
    ];
    $color = $colorMap[$type] ?? $colorMap[null];
@endphp

<div class="flex items-center space-x-2" title="Ø {{ number_format($rating, 1) }}">
    <div class="w-10 h-10 flex items-center justify-center rounded-full {{ $color }} font-bold text-sm">
        {{ number_format($rating, 1) }}
    </div>

    <!-- Schwarzer Balken mit Sternen -->
    <div class="flex items-center space-x-1 px-3 py-1 rounded-full bg-black">
        @for ($i = 0; $i < $full; $i++)
            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.451a1 1 0 00-.364 1.118l1.287 3.955c.3.921-.755 1.688-1.54 1.118l-3.371-2.451a1 1 0 00-1.175 0l-3.371 2.451c-.784.57-1.838-.197-1.539-1.118l1.286-3.955a1 1 0 00-.364-1.118L2.075 9.382c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.955z"/>
            </svg>
        @endfor

        @if ($half)
            <svg class="w-4 h-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                <defs>
                    <linearGradient id="halfGradient">
                        <stop offset="50%" stop-color="currentColor" />
                        <stop offset="50%" stop-color="#1f2937" /> <!-- Tailwind bg-black -->
                    </linearGradient>
                </defs>
                <path fill="url(#halfGradient)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.451a1 1 0 00-.364 1.118l1.287 3.955c.3.921-.755 1.688-1.54 1.118l-3.371-2.451a1 1 0 00-1.175 0l-3.371 2.451c-.784.57-1.838-.197-1.539-1.118l1.286-3.955a1 1 0 00-.364-1.118L2.075 9.382c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.955z"/>
            </svg>
        @endif

        @for ($i = 0; $i < $empty; $i++)
            <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.451a1 1 0 00-.364 1.118l1.287 3.955c.3.921-.755 1.688-1.54 1.118l-3.371-2.451a1 1 0 00-1.175 0l-3.371 2.451c-.784.57-1.838-.197-1.539-1.118l1.286-3.955a1 1 0 00-.364-1.118L2.075 9.382c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.955z"/>
            </svg>
        @endfor
    </div>
</div>
