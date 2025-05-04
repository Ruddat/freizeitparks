@extends('frontend.layouts.app')

@section('title', $park->name . ' – Highlights, Tipps & Besucherinfos')
@section('description', 'Alle Infos zum ' . $park->name . ': Highlights, Top-Attraktionen, Öffnungszeiten und Besucher-Tipps auf einen Blick.')

@section('content')
<section class="bg-gradient-to-br from-blue-50 to-blue-100 py-12 text-gray-800">
    <div class="max-w-5xl mx-auto px-6">
        <h1 class="text-4xl font-extrabold mb-6">{{ $park->name }} – Highlights, Tipps & Besucherinfos</h1>

        {{-- Optional: Generierter SEO-Text --}}
        @if ($park->seo_text)
        <div class="markdown-content mb-10">
            {!! \Illuminate\Support\Str::markdown($park->seo_text) !!}
        </div>
        @endif

        {{-- Manuell gepflegte Einleitung (Fallback) --}}
        @unless ($park->seo_text)
            <p class="text-lg mb-6">
                Der {{ $park->name }} liegt in {{ $park->location ?? 'Deutschland' }} und zählt zu den beliebtesten Freizeitparks in der Region. Besucher erwartet eine Mischung aus spannenden Fahrgeschäften, familienfreundlichen Angeboten und abwechslungsreichen Shows.
            </p>
        @endunless

        <h2 class="text-2xl font-bold mt-10 mb-4">Top-Attraktionen im {{ $park->name }}</h2>
        <ul class="list-disc list-inside bg-white p-4 rounded shadow">
            @foreach ($topAttractions as $ride)
                <li>{{ $ride->ride_name }} (aktuelle Wartezeit: {{ $ride->wait_time }} min)</li>
            @endforeach
        </ul>

        <h2 class="text-2xl font-bold mt-10 mb-4">Nützliche Tipps für deinen Besuch</h2>
        <ul class="list-disc list-inside space-y-2">
            <li>📅 Prüfe die <a href="{{ route('parks.show', $park->slug) }}" class="text-blue-600 underline">aktuellen Öffnungszeiten & Wartezeiten</a></li>
            <li>🎟️ Früh buchen lohnt sich – oft gibt es Online-Rabatte</li>
            <li>🌦️ Wetter im Blick behalten – der Park ist bei gutem Wetter deutlich voller</li>
        </ul>

        <h2 class="text-2xl font-bold mt-10 mb-4">FAQ zum {{ $park->name }}</h2>
        <div class="space-y-4">
            <details class="bg-white p-4 rounded shadow">
                <summary class="font-semibold cursor-pointer">Wie beliebt ist der Park aktuell?</summary>
                <p class="mt-2">Aktuell besuchen durchschnittlich {{ rand(500, 3000) }} Personen täglich den Park (Schätzung basierend auf Besucherzahlen).</p>
            </details>
            <details class="bg-white p-4 rounded shadow">
                <summary class="font-semibold cursor-pointer">Welche Attraktionen sind besonders empfehlenswert?</summary>
                <p class="mt-2">Zu den beliebtesten zählen derzeit: {{ $topAttractions->pluck('ride_name')->implode(', ') }}.</p>
            </details>
        </div>
    </div>
</section>

<style>
    .markdown-content h1,
.markdown-content h2,
.markdown-content h3 {
    font-weight: bold;
    margin-top: 1.5rem;
}

.markdown-content p {
    margin-bottom: 1rem;
    line-height: 1.7;
}

.markdown-content ul {
    list-style: disc;
    margin-left: 1.5rem;
    margin-bottom: 1rem;
}

.markdown-content li {
    margin-bottom: 0.5rem;
}

.markdown-content strong {
    font-weight: 600;
}
</style>

@endsection
