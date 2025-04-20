<div x-data x-cloak class="fixed bottom-4 right-4 z-50">
    <div class="bg-white text-gray-700 rounded-lg shadow-lg px-4 py-3 text-sm flex items-center gap-2 border border-green-300 animate-slide-in-down"
         x-show="$store.debug?.showCrowdNotice ?? false" x-transition.duration.500ms>
        🛰️ Besuch erfasst – Danke!
        <button class="ml-2 text-gray-500 hover:text-red-500" @click="$store.debug.showCrowdNotice = false">
            &times;
        </button>
    </div>


<script>
    // Nur zeigen, wenn du möchtest
    document.addEventListener('alpine:init', () => {
        Alpine.store('debug', {
            showCrowdNotice: true
        });

        setTimeout(() => Alpine.store('debug').showCrowdNotice = false, 6000);
    });
</script>

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
</style>
</div>
