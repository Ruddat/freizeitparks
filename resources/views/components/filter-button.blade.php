@props(['active', 'wire:click', 'label', 'color' => 'gray'])

<button
    {{ $attributes }}
    class="px-3 py-1 rounded-full border text-sm transition-colors
           {{ $active ? "bg-{$color}-600 text-white border-{$color}-600" : "text-gray-700 border-gray-300 hover:bg-gray-100" }}"
>
    {{ $label }}
</button>
