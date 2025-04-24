<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($urls as $url)
    <url>
        <loc>{{ $url['loc'] }}</loc>
        @if(!empty($url['lastmod']))
            <lastmod>{{ $url['lastmod'] }}</lastmod>
        @endif
        <changefreq>{{ $url['changefreq'] ?? 'monthly' }}</changefreq>
        <priority>{{ $url['priority'] ?? '0.5' }}</priority>
    </url>
@endforeach
</urlset>
