<div>
@if($showNotification)
<div
    x-data="{ visible: true }"
    x-cloak
    class="fixed bottom-4 right-4 z-50"
>
    <div
        class="bg-yellow-300 text-black border-4 border-black shadow-comic rounded-xl px-5 py-3 text-base font-comic flex items-center gap-3"
        x-show="visible"
        x-transition.duration.500ms
        x-init="setTimeout(() => visible = false, 6000)"
    >
        <span class="text-xl">🛰️</span>
        <span>Besuch erfasst – Danke!</span>

        <button
            class="ml-auto text-black hover:text-red-600 text-xl font-bold leading-none"
            @click="visible = false"
        >
            &times;
        </button>
    </div>
</div>
@endif

<style>
    @keyframes slide-in-down {
        0% {
            transform: translateY(20px);
            opacity: 0;
        }
        100% {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .animate-slide-in-down {
        animation: slide-in-down 0.4s ease-out;
    }

    .shadow-comic {
        box-shadow: 6px 6px 0px #000;
    }

    .font-comic {
        font-family: 'Comic Neue', 'Comic Sans MS', cursive;
    }
</style>
</div>
