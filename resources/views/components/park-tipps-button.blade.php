@props(['slug', 'label' => '💡 Tipps & Highlights'])

<a
    href="{{ route('themen.park', ['slug' => $slug]) }}"
    class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold text-sm rounded shadow transition"
>
    {{ $label }}
</a>
