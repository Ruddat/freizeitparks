@props(['paginator'])

@if ($paginator->hasPages())
<nav class="flex flex-wrap justify-center items-center gap-1 sm:gap-2 mt-6 sticky bottom-0 bg-[#010b3f] py-2 z-50" aria-label="Seitennavigation">
            {{-- Zurück --}}
        @if ($paginator->onFirstPage())
            <span class="px-2 py-1 sm:px-3 sm:py-2 bg-gray-700 text-gray-400 rounded-md cursor-not-allowed text-sm sm:text-base">
                ← Zurück
            </span>
        @else
            <button wire:click="previousPage"
                    class="px-2 py-1 sm:px-3 sm:py-2 bg-yellow-400 text-black font-semibold rounded-md hover:bg-yellow-300 transition text-sm sm:text-base min-w-[80px] sm:min-w-[100px] flex justify-center items-center">
                ← Zurück
            </button>
        @endif

        {{-- Seitenzahlen (Desktop: mehr sichtbar, Mobile: nur aktuelle und Nachbarn) --}}
        <div class="hidden sm:flex items-center gap-1">
            @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="px-3 py-2 bg-white text-[#010b3f] font-bold rounded-md shadow-md">
                        {{ $page }}
                    </span>
                @elseif ($page <= 2 || $page >= $paginator->lastPage() - 1 || abs($page - $paginator->currentPage()) <= 1)
                    <button wire:click="gotoPage({{ $page }})"
                            class="px-3 py-2 bg-[#021262] text-white rounded-md hover:bg-[#12257a] transition">
                        {{ $page }}
                    </button>
                @elseif ($page == 3 || $page == $paginator->lastPage() - 2)
                    <span class="px-2 py-1 text-white">…</span>
                @endif
            @endforeach
        </div>

        {{-- Seitenzahlen (Mobile: nur aktuelle Seite und direkte Nachbarn) --}}
        <div class="flex sm:hidden items-center gap-1">
            @if ($paginator->currentPage() > 1)
                <button wire:click="gotoPage({{ $paginator->currentPage() - 1 }})"
                        class="px-2 py-1 bg-[#021262] text-white rounded-md hover:bg-[#12257a] transition text-sm">
                    {{ $paginator->currentPage() - 1 }}
                </button>
            @endif

            <span class="px-2 py-1 bg-white text-[#010b3f] font-bold rounded-md shadow-md text-sm">
                {{ $paginator->currentPage() }}
            </span>

            @if ($paginator->currentPage() < $paginator->lastPage())
                <button wire:click="gotoPage({{ $paginator->currentPage() + 1 }})"
                        class="px-2 py-1 bg-[#021262] text-white rounded-md hover:bg-[#12257a] transition text-sm">
                    {{ $paginator->currentPage() + 1 }}
                </button>
            @endif
        </div>

        {{-- Weiter --}}
        @if ($paginator->hasMorePages())
            <button wire:click="nextPage"
                    class="px-2 py-1 sm:px-3 sm:py-2 bg-yellow-400 text-black font-semibold rounded-md hover:bg-yellow-300 transition text-sm sm:text-base min-w-[80px] sm:min-w-[100px] flex justify-center items-center">
                Weiter →
            </button>
        @else
            <span class="px-2 py-1 sm:px-3 sm:py-2 bg-gray-700 text-gray-400 rounded-md cursor-not-allowed text-sm sm:text-base">
                Weiter →
            </span>
        @endif
    </nav>
@endif
