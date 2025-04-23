<div aria-live="polite" aria-atomic="true" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
    <div class="toast-container">
        @foreach (['success', 'error', 'warning', 'info'] as $type)
            @if (session()->has($type))
                <div class="toast show align-items-center text-white bg-{{ $type }} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            {{ session($type) }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
