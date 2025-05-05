@extends('frontend.layouts.app')

@section('title', 'Park-Trend Widgets zum Einbinden')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4 text-gray-800">
    <h1 class="text-3xl font-bold mb-4">🔌 Park-Trend Widgets</h1>

    <p class="text-gray-600 mb-6">
        Du kannst diese kompakten Info-Widgets kostenlos auf deiner Website, deinem Blog oder Forum einbinden.
        Sie zeigen <strong>aktuelle Trends, Besucherinfos und Wetterdaten</strong> – automatisch aktuell.
    </p>

    <p class="text-sm text-gray-500 italic mb-10">
        Die Daten basieren auf Auslastungsschätzungen, Wetterdaten und Besucherberichten, gesammelt von parkverzeichnis.de.
    </p>

    @foreach ($parks as $park)
        <div class="mb-10 border border-gray-200 rounded-lg p-4 bg-white shadow-md">
            <h2 class="text-xl font-semibold mb-3 text-gray-900">📍 {{ $park->name }}</h2>

            <div class="flex flex-col lg:flex-row gap-4">
                {{-- Vorschau --}}
                <iframe
                    src="{{ route('widgets.trend', $park) }}"
                    width="300" height="180"
                    style="border:1px solid #ccc; border-radius: 6px;"
                    title="Widget {{ $park->name }}"
                    loading="lazy"
                    onerror="this.parentNode.innerHTML = '<div class=\'text-red-600\'>🚫 Keine Widget-Daten verfügbar.</div>';"
                ></iframe>

                {{-- Embed Code + Button --}}
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Einbettungscode:
                    </label>
                    <textarea readonly onclick="this.select()" class="w-full text-sm font-mono p-2 bg-gray-50 border border-gray-300 rounded">
<iframe src="{{ route('widgets.trend', $park) }}" width="300" height="180" style="border:0;" title="Trend Widget – {{ $park->name }}"></iframe>
                    </textarea>
                    <button onclick="copyToClipboard(this)"
                            class="mt-2 px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                        📋 Code kopieren
                    </button>
                    <p class="text-xs text-gray-500 mt-2">
                        Einfach in den HTML-Code deiner Seite einfügen.
                    </p>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Pagination --}}
    <div class="mt-12">
        {{ $parks->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyToClipboard(button) {
    const textarea = button.previousElementSibling;
    textarea.select();
    document.execCommand("copy");
    button.textContent = '✅ Kopiert!';
    setTimeout(() => button.textContent = '📋 Code kopieren', 2000);
}
</script>
@endpush
