<div>
    @if ($success)
        <div class="p-4 mb-4 text-green-700 bg-green-100 rounded">
            ✅ Vielen Dank! Wir benachrichtigen Sie, sobald die API verfügbar ist.
        </div>
    @else
        <form wire:submit.prevent="submit" class="space-y-4 max-w-xl">
            <div>
                <label class="block text-sm font-medium text-gray-700">E-Mail-Adresse *</label>
                <input type="email" wire:model="email" class="w-full border rounded px-3 py-2 mt-1">
                @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Name des Parks (optional)</label>
                <input type="text" wire:model="park_name" class="w-full border rounded px-3 py-2 mt-1">
                @error('park_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="inline-flex items-start">
                    <input type="checkbox" wire:model="agreed_to_privacy" class="mt-1 mr-2">
                    <span class="text-sm text-gray-600">
                        Ich stimme der Verarbeitung meiner Daten gemäß der
                        <a href="/datenschutz" class="underline" target="_blank">Datenschutzerklärung</a> zu.
                    </span>
                </label>
                @error('agreed_to_privacy') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition">
                ✅ Jetzt vormerken lassen
            </button>
        </form>
    @endif
</div>
