@props(['status'])

@php
    $label = match($status) {
        'open' => '🟢 Geöffnet',
        'closed' => '🔴 Geschlossen',
        default => '⚪ Unbekannt',
    };

    $classes = match($status) {
        'open' => 'bg-green-600/20 text-green-300',
        'closed' => 'bg-red-600/20 text-red-300',
        default => 'bg-gray-600/20 text-gray-300',
    };
@endphp

<span class="inline-block {{ $classes }} px-3 py-1 rounded-full text-xs font-semibold mt-2">
    {{ $label }}
</span>
