@if ($paginator->hasPages())
    <div class="d-flex justify-content-center justify-content-md-end mt-3">
        <ul class="pagination app-pagination pagination-sm flex-wrap">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link b-r-left">Previous</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link b-r-left" wire:click="previousPage" wire:loading.attr="disabled" rel="prev">Previous</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- Dots --}}
                @if (is_string($element))
                    <li class="page-item disabled d-none d-md-block"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Page Numbers --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item d-none d-md-block">
                                <button class="page-link" wire:click="gotoPage({{ $page }})">{{ $page }}</button>
                            </li>
                            @if (abs($page - $paginator->currentPage()) <= 1)
                                <li class="page-item d-block d-md-none">
                                    <button class="page-link" wire:click="gotoPage({{ $page }})">{{ $page }}</button>
                                </li>
                            @endif
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" wire:click="nextPage" wire:loading.attr="disabled" rel="next">Next</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Next</span>
                </li>
            @endif
        </ul>
    </div>
@endif
