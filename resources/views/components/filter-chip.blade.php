@props(['condition', 'label', 'wire:click', 'color' => 'blue'])

@if($condition)
    <span class="bg-{{ $color }}-100 text-{{ $color }}-800 px-3 py-1 rounded-full flex items-center">
        {{ $label }}
        <button {{ $attributes }} class="ml-2 text-{{ $color }}-500 hover:text-{{ $color }}-700 transition-colors">×</button>
    </span>
@endif
