<div>
    @if ($success)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-xl p-6 max-w-lg shadow-xl text-center">
                <h2 class="text-2xl font-bold text-green-600 mb-4">Vielen Dank!</h2>
                <p class="text-gray-700">Deine Nachricht wurde erfolgreich gesendet.</p>
                <button wire:click="$set('success', false)" class="mt-6 px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition">
                    Schließen
                </button>
            </div>
        </div>
    @elseif ($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-xl p-6 max-w-lg w-full shadow-xl relative">
                <button wire:click="close" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl">×</button>
                <h2 class="text-2xl font-semibold text-[#080e3c] mb-4">Kontaktiere uns</h2>

                <form wire:submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" wire:model.defer="name" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">E-Mail</label>
                        <input type="email" wire:model.defer="email" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nachricht</label>
                        <textarea wire:model.defer="message" rows="4" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500"></textarea>
                        @error('message') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Anhang (optional)</label>
                        <input type="file" wire:model="attachment" class="mt-1 block w-full text-sm text-gray-500">
                        @error('attachment') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition">
                            Nachricht senden
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
