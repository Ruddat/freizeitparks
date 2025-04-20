<div>
    <!-- Bewertungs-Button -->
    <div class="relative">
        <button
            wire:click="$set('openRatingModal', true)"
            class="text-sm text-yellow-400 font-bold hover:underline"
        >
            📝 Jetzt bewerten
        </button>
    </div>

    <!-- Modal -->
    @if($openRatingModal)
    <div
    class="fixed inset-0 z-50 bg-black bg-opacity-60 flex items-center justify-center"
    @click.self="$wire.set('openRatingModal', false)"
>
            <div
                id="modal-box"
                class="bg-[#0d0f3f] text-white w-full max-w-sm rounded-2xl p-6 shadow-xl space-y-4"
            >
                <h3 class="text-xl font-bold text-center">Bewertung abgeben</h3>

                <form wire:submit.prevent="submit" class="space-y-4">
                    <!-- Andrang -->
                    <div>
                        <label class="block font-semibold mb-1">
                            Wie voll ist es gerade?
                        </label>
                        <select
                            wire:model="crowd_level"
                            class="w-full rounded bg-[#1c1e5c] text-white border-none"
                        >
                            <option value="1">😴 Sehr leer</option>
                            <option value="2">🙂 Locker besucht</option>
                            <option value="3">😎 Normal</option>
                            <option value="4">😰 Ziemlich voll</option>
                            <option value="5">😱 Überfüllt</option>
                        </select>
                    </div>

                    <!-- Kommentar -->
                    <textarea
                        wire:model.defer="comment"
                        rows="3"
                        placeholder="Optionaler Kommentar..."
                        class="w-full rounded bg-[#1c1e5c] text-white placeholder-gray-400 border-none resize-none"
                    ></textarea>

                    <!-- Bewertungs-Sterne -->
                    @php
                        $fields = [
                            'attractiveness' => ['🎡', 'Attraktivität'],
                            'theming'        => ['🎭', 'Thematisierung'],
                            'gastronomy'     => ['🍔', 'Gastronomie'],
                            'cleanliness'    => ['🧼', 'Sauberkeit'],
                            'service'        => ['🤝', 'Service'],
                        ];
                    @endphp

                    @foreach ($fields as $field => [$icon, $label])
                        <div>
                            <label class="block font-medium mb-1">
                                {{ $icon }} {{ $label }}
                            </label>
                            <div class="flex gap-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg
                                        wire:click="$set('{{ $field }}', {{ $i }})"
                                        wire:key="star-{{ $field }}-{{ $i }}"
                                        class="w-6 h-6 cursor-pointer transition-all duration-150 transform hover:scale-110 {{ $this->{$field} >= $i ? 'text-yellow-400' : 'text-gray-600' }} fill-current"
                                        viewBox="0 0 20 20"
                                    >
                                        <path d="M10 15l-5.878 3.09 1.122-6.545L.488 6.91l6.563-.955L10 0l2.949 5.955 6.563.955-4.756 4.635 1.122 6.545z"/>
                                    </svg>
                                @endfor
                            </div>
                        </div>
                    @endforeach

                    <!-- Abschicken -->
                    <div class="text-center pt-2">
                        <button
                            type="submit"
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition"
                        >
                            Abschicken
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- JavaScript für Modal stopPropagation -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            const modalBox = document.getElementById('modal-box');
            if (modalBox) {
                modalBox.addEventListener('click', (event) => {
                    event.stopPropagation();
                });
            }
        });
    </script>
</div>
