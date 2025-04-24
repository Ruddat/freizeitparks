{{-- Dynamische SEO-Daten --}}
@if (isset($seo))
    <title>{{ $seo['title'] ?? 'Parkverzeichnis.de – Freizeitparks entdecken' }}</title>
    <meta name="description" content="{{ $seo['description'] ?? 'Entdecke Freizeitparks in Europa: Öffnungszeiten, Bewertungen, Videos & mehr.' }}">
    <link rel="canonical" href="{{ $seo['canonical'] ?? url()->current() }}">

    {{-- OpenGraph & Twitter --}}
    @foreach ($seo['extra_meta'] ?? [] as $key => $value)
        <meta property="{{ $key }}" content="{{ $value }}">
    @endforeach

    {{-- Keywords --}}
    <meta name="keywords" content="{{ implode(', ', $seo['keywords']['tags'] ?? ['Freizeitpark', 'Öffnungszeiten', 'Attraktionen']) }}">
    @foreach ($seo['keywords'] ?? [] as $key => $value)
        @if ($key !== 'tags')
            <meta name="keyword-{{ $key }}" content="{{ is_array($value) ? implode(', ', $value) : $value }}">
        @endif
    @endforeach
@else
    {{-- Default-Fallback --}}
    <title>Parkverzeichnis.de – Freizeitparks entdecken</title>
    <meta name="description" content="Entdecke Freizeitparks in Europa: Öffnungszeiten, Bewertungen, Videos & mehr.">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="keywords" content="Freizeitpark, Öffnungszeiten, Attraktionen">
@endif

{{-- Strukturierte Daten für Schema.org --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type":
        @php
            echo isset($park) && $park instanceof \App\Models\Park ? '"AmusementPark"' : '"WebSite"';
        @endphp,
    "name": "{{ $seo['title'] ?? ($park->name ?? 'Parkverzeichnis.de') }}",
    "image": "{{ $seo['image'] ?? asset('img/default-bg.jpg') }}",
    "description": "{{ $seo['description'] ?? 'Freizeitparks in Europa entdecken – Bewertungen, Öffnungszeiten & Tipps' }}",
    @if(isset($park))
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "{{ $park->city ?? $park->location }}",
            "addressCountry": "{{ $park->country ?? 'DE' }}"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": "{{ $park->latitude }}",
            "longitude": "{{ $park->longitude }}"
        },
    @endif
    "url": "{{ $seo['canonical'] ?? url()->current() }}",
    "touristType": "Leisure",
    "keywords": "{{ implode(', ', $seo['keywords']['tags'] ?? ['Freizeitpark', 'Attraktionen', 'Bewertungen']) }}"
    @if(isset($seo['keywords']['nextYear']))
        , "temporalCoverage": "{{ $seo['keywords']['nextYear'] }}-01-01/{{ $seo['keywords']['nextYear'] }}-12-31"
    @endif
    @php
        $extraProps = collect($seo['keywords'] ?? [])->except(['tags', 'nextYear', 'main', 'description']);
    @endphp
    @if($extraProps->count())
        , "additionalProperty": [
            @foreach ($extraProps as $key => $value)
                {
                    "@type": "PropertyValue",
                    "name": "{{ $key }}",
                    "value": "{{ is_array($value) ? implode(', ', $value) : $value }}"
                }@if (!$loop->last),@endif
            @endforeach
        ]
    @endif
    @php
    $socialProfiles = app(\App\Services\SeoService::class)->getSocialProfiles();
@endphp

@if (count($socialProfiles))
    ,"sameAs": [
        @foreach ($socialProfiles as $link)
            "{{ $link }}"@if (!$loop->last),@endif
        @endforeach
    ]
@endif

}
</script>
