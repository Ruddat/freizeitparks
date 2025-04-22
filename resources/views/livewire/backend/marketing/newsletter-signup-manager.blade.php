<div class="p-4">
    <h1 class="text-2xl font-bold mb-4">📬 Newsletter-Anmeldungen</h1>

    <div class="mb-4">
        <input type="text" wire:model.debounce.500ms="search"
               class="w-full md:w-1/3 p-2 border rounded shadow-sm"
               placeholder="Suche nach E-Mail...">
    </div>

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full text-sm text-gray-800">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-4 py-2">📧 E-Mail</th>
                    <th class="px-4 py-2">🧑 Name</th>
                    <th class="px-4 py-2">🏡 Ort</th>
                    <th class="px-4 py-2">🎯 Interessen</th>
                    <th class="px-4 py-2">✅ Bestätigt</th>
                    <th class="px-4 py-2">📅 Angemeldet am</th>
                </tr>
            </thead>
            <tbody>
                @forelse($signups as $signup)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $signup->email }}</td>
                        <td class="px-4 py-2">{{ $signup->name }}</td>
                        <td class="px-4 py-2">{{ $signup->city }}</td>
                        <td class="px-4 py-2">
                            @if($signup->interests)
                                <ul class="list-disc list-inside">
                                    @foreach($signup->interests as $interest)
                                        <li>{{ ucfirst($interest) }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            @if($signup->confirmed_at)
                                <span class="text-green-600 font-semibold">Ja</span>
                            @else
                                <span class="text-red-500">Nein</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $signup->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                            Keine Einträge gefunden.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $signups->links() }}
    </div>
</div>
