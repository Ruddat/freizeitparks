<div x-data="{ open: false }" class="bg-[#0d0f3f] rounded-2xl p-4 sm:p-6 shadow-xl border border-[#2f3a8c] text-white space-y-6">
    <!-- Titel -->
    <h3 class="text-2xl sm:text-3xl font-bold text-center">Bewertungen</h3>

    <!-- Durchschnitt & Kategorien -->
    <div class="flex flex-col sm:items-center md:flex-row justify-between items-start md:items-center gap-6">
        <div class="bg-[#1c1e5c] rounded-xl p-4 px-6 shadow text-center w-full sm:w-auto">
            <div class="text-3xl sm:text-4xl text-yellow-400 font-bold flex items-center justify-center gap-2">
                ⭐ {{ $gesamtAvg }}
            </div>
            <div class="text-sm text-gray-300">{{ $anzahl }} Bewertungen</div>
        </div>
        <div class="grid grid-cols-2 sm:flex flex-wrap justify-center md:justify-start gap-4 sm:gap-6">
            @foreach ($kategorien as $item)
                <div class="flex flex-col items-center space-y-1">
                    <div class="w-14 h-14 rounded-full text-white flex items-center justify-center font-bold text-lg" style="background-color: {{ $item['color'] }}">
                        {{ $item['value'] }}
                    </div>
                    <div class="text-sm text-gray-200 text-center">{{ $item['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Metriken-Balken -->
    <div class="space-y-3">
        @php
        $metrics = [
            ['value' => $avgCrowd,   'icon' => '👥', 'label' => 'Andrang',        'color' => $crowdColor],
            ['value' => $avgTheming, 'icon' => '🎨', 'label' => 'Themenbereich', 'color' => '#4646e6'],
            ['value' => $avgClean,   'icon' => '🧼', 'label' => 'Sauberkeit',     'color' => '#3d77f3'],
            ['value' => $avgGastro,  'icon' => '🍽️','label' => 'Gastronomie',    'color' => '#d23ba8'],
            ['value' => $avgService, 'icon' => '🛎️', 'label' => 'Service',        'color' => '#f5c12b'],
            ['value' => $avgAttra,   'icon' => '✨', 'label' => 'Attraktivität',  'color' => '#9c27b0'],
        ];
        @endphp

        @foreach ($metrics as $m)
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-6">
                <div class="w-full sm:w-32 flex items-center gap-2 text-sm text-gray-200">
                    <span class="text-lg">{{ $m['icon'] }}</span>
                    <span>{{ $m['label'] }}</span>
                </div>
                <div class="w-full sm:flex-1 bg-gray-700 h-2 rounded-full overflow-hidden">
                    <div
                        class="h-2"
                        style="width: {{ round($m['value'] / 5 * 100) }}%; background-color: {{ $m['color'] }}"
                    ></div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Neuste Kommentare -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 pt-2 sm:pt-4">
        @foreach ($kommentare as $kom)
            <div class="bg-[#1c1e5c] rounded-lg p-4">
                <div class="flex gap-1 text-yellow-400 mb-1">
                    @for ($i = 0; $i < $kom['stars']; $i++)
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09 1.122-6.545L.488 6.91l6.563-.955L10 0l2.949 5.955 6.563.955-4.756 4.635 1.122 6.545z"/>
                        </svg>
                    @endfor
                </div>
                <div class="text-sm text-gray-400 mb-1">{{ $kom['date'] }}</div>
                <div class="text-white">{{ $kom['text'] }}</div>
            </div>
        @endforeach
    </div>

    <!-- Footer & Aktionen -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mt-4 gap-4">
        <livewire:frontend.park-andrang-component :park="$park" />

        <a href="#" @click.prevent="open = true" class="text-sm sm:text-base text-blue-300 underline hover:text-blue-400 block sm:inline">
            ➤ Alle Bewertungen anzeigen
        </a>
    </div>

    <!-- Modal -->
    <div x-show="open" x-transition class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-1500" style="display: none;">

        <div class="bg-[#0d0f3f] text-white rounded-xl shadow-xl w-[95%] sm:w-full max-w-3xl max-h-[90vh] overflow-y-auto p-4 sm:p-6 relative" @click.away="open = false">
            <button @click="open = false" class="absolute top-3 right-3 text-gray-400 hover:text-white text-2xl">&times;</button>
            <h2 class="text-xl sm:text-2xl font-bold mb-4 text-center">Alle Bewertungen</h2>
            <div class="space-y-4">
                @foreach ($allComments as $r)
                    @php $stars = (int) round(collect([$r->theming, $r->cleanliness, $r->gastronomy, $r->service])->avg()); @endphp
                    <div class="bg-[#1c1e5c] p-4 rounded-lg">
                        <div class="flex justify-between items-center mb-1">
                            <div class="flex gap-1 text-yellow-400">
                                @for ($j = 0; $j < $stars; $j++)
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.122-6.545L.488 6.91l6.563-.955L10 0l2.949 5.955 6.563.955-4.756 4.635 1.122 6.545z"/>
                                    </svg>
                                @endfor
                            </div>
                            <div class="text-sm text-gray-400">{{ $r->created_at->format('d. M. Y') }}</div>
                        </div>
                        <div class="text-gray-100">{{ $r->comment }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
