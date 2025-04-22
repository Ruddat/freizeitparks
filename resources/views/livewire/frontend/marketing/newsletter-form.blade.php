<div>
    <!-- Schritt 1: E-Mail -->
    <form wire:submit.prevent="submitEmail" class="flex mt-6">
        <input type="email" wire:model.defer="email" placeholder="Deine Email"
            class="px-3 py-2 text-sm bg-gray-700 text-white placeholder-gray-400 rounded-l focus:outline-none focus:ring-1 focus:ring-yellow-400 w-full"
        >
        <button type="submit"
            class="bg-yellow-500 hover:bg-yellow-600 text-gray-900 font-medium px-3 py-2 rounded-r text-sm transition-all duration-300 transform hover:scale-105"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </button>

    </form>

    @error('email')
    <div class="mt-2 text-sm text-pink-400 animate-pulse">
        {{ $message }}
    </div>
    @enderror

    <!-- Erfolgsmeldung -->
    @if(session()->has('success'))
        <div class="mt-4 text-green-400 text-sm flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Modal -->
    <div x-data="{ open: @entangle('showModal') }">
        <div x-show="open" x-cloak
             x-transition.opacity.scale
             class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/50"
        >
            <div @click.away="open = false"
                 class="bg-white rounded-lg p-6 w-full max-w-md shadow-xl"
            >
                <!-- Titel -->
                <div class="flex items-center space-x-2 mb-4">
                    <span class="text-2xl">📬</span>
                    <h2 class="text-xl font-bold text-yellow-500">Fast geschafft!</h2>
                </div>

                <!-- Formular -->
                <form wire:submit.prevent="register" class="space-y-4 text-sm text-gray-800">
                    <input type="text" wire:model.defer="name" placeholder="Dein Name oder Nickname"
                        class="w-full border border-gray-300 rounded p-2 text-sm text-gray-800 placeholder-gray-400" />
                        @if($nameHint)
                        <div class="text-yellow-500 text-sm mt-1 animate-pulse">
                            {{ $nameHint }}
                        </div>
                    @endif

                    @error('name')
                        <div class="text-red-500 text-sm mt-1 animate-bounce">
                            {{ $message }}
                        </div>
                    @enderror
                    <input type="text" wire:model.defer="city" placeholder="Dein Ort (optional)"
                        class="w-full border border-gray-300 rounded p-2 text-sm text-gray-800 placeholder-gray-400" />

                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">Was interessiert dich?</label>
                        <div class="flex flex-wrap gap-3">
                            <label class="flex items-center space-x-2 text-sm">
                                <input type="checkbox" wire:model="interests" value="coupons"
                                    class="accent-yellow-500 w-4 h-4 rounded border-gray-300">
                                <span>Coupons</span>
                            </label>
                            <label class="flex items-center space-x-2 text-sm">
                                <input type="checkbox" wire:model="interests" value="news"
                                    class="accent-yellow-500 w-4 h-4 rounded border-gray-300">
                                <span>Park-News</span>
                            </label>
                            <label class="flex items-center space-x-2 text-sm">
                                <input type="checkbox" wire:model="interests" value="events"
                                    class="accent-yellow-500 w-4 h-4 rounded border-gray-300">
                                <span>Events</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit"
                        class="bg-yellow-500 hover:bg-yellow-600 text-black px-4 py-2 rounded w-full font-medium transition transform hover:scale-105"
                    >
                        Jetzt registrieren
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
