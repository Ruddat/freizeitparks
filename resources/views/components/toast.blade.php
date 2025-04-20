@props([
    'position' => 'top-right' // z. B. top-left, bottom-right, bottom-center
])

@php
    $positions = [
        'top-left' => 'top-4 left-4 items-start',
        'top-right' => 'top-4 right-4 items-start',
        'top-center' => 'top-4 left-1/2 -translate-x-1/2 items-center',
        'bottom-left' => 'bottom-4 left-4 items-end',
        'bottom-right' => 'bottom-4 right-4 items-end',
        'bottom-center' => 'bottom-4 left-1/2 -translate-x-1/2 items-center',
    ];
@endphp

<div
    x-data="{
        toasts: [],
        addToast(type, message) {
            const id = Date.now();
            this.toasts.push({ id, type, message });

            this.playSound(type);

            if (navigator.vibrate && (type === 'error' || type === 'warning')) {
                navigator.vibrate([100, 50, 100]);
            }

            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 5000);
        },
        playSound(type) {
            let url = null;
            switch (type) {
                case 'success': url = '/sounds/success.mp3'; break;
                case 'error':   url = '/sounds/error.mp3'; break;
                case 'info':    url = '/sounds/info.mp3'; break;
                case 'warning': url = '/sounds/warning.mp3'; break;
            }
            if (url) {
                const audio = new Audio(url);
                audio.volume = 0.5;
                audio.play().catch(() => {});
            }
        }
    }"
    x-init="window.addEventListener('show-toast', e => addToast(e.detail.type || 'info', e.detail.message || ''))"
    class="fixed z-[9999] flex flex-col gap-3 max-w-sm w-[90vw] sm:w-[22rem] {{ $positions[$position] ?? $positions['top-right'] }}"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative flex items-start gap-3 rounded-xl px-5 py-4 shadow-lg text-sm border bg-white dark:bg-gray-800"
            :class="{
                'border-green-500': toast.type === 'success',
                'border-red-500': toast.type === 'error',
                'border-blue-500': toast.type === 'info',
                'border-yellow-400': toast.type === 'warning'
            }"
        >
            <div class="text-xl leading-none pt-0.5">
                <template x-if="toast.type === 'success'">✅</template>
                <template x-if="toast.type === 'error'">❌</template>
                <template x-if="toast.type === 'info'">ℹ️</template>
                <template x-if="toast.type === 'warning'">⚠️</template>
            </div>

            <div class="flex-1 text-gray-800 dark:text-gray-100">
                <div class="font-semibold mb-1" x-text="toast.type === 'success' ? 'Erfolg' :
                                                         toast.type === 'error' ? 'Fehler' :
                                                         toast.type === 'warning' ? 'Hinweis' : 'Info'"></div>
                <div x-text="toast.message"></div>
            </div>

            <button
                @click="toasts = toasts.filter(t => t.id !== toast.id)"
                class="absolute top-2 right-2 text-gray-400 hover:text-black dark:hover:text-white text-xl"
                title="Schließen"
            >&times;</button>
        </div>
    </template>
</div>
