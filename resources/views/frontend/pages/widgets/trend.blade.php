<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Park-Trend Widget – {{ $park->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: sans-serif; margin: 0; padding: 10px; font-size: 14px; background: #fff; color: #333; }
        .widget { border: 1px solid #ccc; border-radius: 8px; padding: 12px; max-width: 320px; }
        .title { font-weight: bold; font-size: 16px; margin-bottom: 8px; }
        .row { margin-bottom: 6px; }
        .footer { font-size: 11px; color: #888; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="widget">
        <div class="title">📊 {{ $park->name }} – Heute</div>
        <div class="row">⏱️ Ø Wartezeit: {{ $avgWait ? round($avgWait) . ' min' : 'n/a' }}</div>
        <div class="row">🌤️ Wetter: {{ $weather?->description ?? '–' }} – {{ $weather?->temp_day }}°C</div>
        <div class="row">🧍 Auslastung:
            @if($stats?->avg_crowd_level)
                Level {{ $stats->avg_crowd_level }}
            @else
                keine Angabe
            @endif
        </div>
        <div class="footer">powered by <a href="https://parkverzeichnis.de" target="_blank">parkverzeichnis.de</a></div>
    </div>
</body>
</html>
