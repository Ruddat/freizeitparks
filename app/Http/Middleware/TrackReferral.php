<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ModReferralLog;
use GeoIp2\Database\Reader;
use WhichBrowser\Parser;

class TrackReferral
{
    public function handle(Request $request, Closure $next)
    {
        $referer = $request->header('referer');
        $landingPage = $request->fullUrl();
        $ipAddress = $request->ip();
        $userAgent = $request->header('User-Agent') ?? '';
        $userId = auth()->id();

        $source = 'direct';
        $keyword = null;
        $isBot = $this->isBot($userAgent);

        if ($isBot) {
            $source = 'bot';
        } elseif ($referer) {
            $parsedReferer = parse_url($referer);
            $host = $parsedReferer['host'] ?? '';

            if (str_contains($host, 'google')) {
                $source = 'google';
            } elseif (str_contains($host, 'bing')) {
                $source = 'bing';
            } elseif (str_contains($host, 'yahoo')) {
                $source = 'yahoo';
            }

            if (isset($parsedReferer['query'])) {
                parse_str($parsedReferer['query'], $params);
                $keyword = $params['q'] ?? null;
            }
        }

        // Geo-Location
        $geo = [
            'country' => null,
            'city' => null,
            'asn' => null,
            'isp' => null,
        ];

        try {
            $readerCity = new Reader(storage_path('GeoLite2-City.mmdb'));
            $readerASN = new Reader(storage_path('GeoLite2-ASN.mmdb'));

            $recordCity = $readerCity->city($ipAddress);
            $recordASN = $readerASN->asn($ipAddress);

            $geo['country'] = $recordCity->country->name ?? null;
            $geo['city'] = $recordCity->city->name ?? null;
            $geo['asn'] = $recordASN->autonomousSystemNumber ?? null;
            $geo['isp'] = $recordASN->autonomousSystemOrganization ?? null;
        } catch (\Exception $e) {
            // Fehler bei Geo-IP → kein Drama
        }

        // Geräteinfos
        $parser = new Parser($userAgent);
        $deviceType = $parser->device->type ?? null;
        $os = $parser->os->name ?? null;
        $browser = $parser->browser->name ?? null;

        // Bestehender Log?
        $existingLog = ModReferralLog::where([
            'user_id' => $userId,
            'referer_url' => $referer,
            'source' => $source,
            'keyword' => $keyword,
            'landing_page' => $landingPage,
            'ip_address' => $ipAddress,
        ])->first();

        if ($existingLog) {
            $existingLog->increment('visit_count');
            $existingLog->update(['visited_at' => now()]);
        } else {
            ModReferralLog::create([
                'user_id' => $userId,
                'referer_url' => $referer,
                'source' => $source,
                'keyword' => $keyword,
                'landing_page' => $landingPage,
                'ip_address' => $ipAddress,
                'is_bot' => $isBot ? 1 : 0,
                'visited_at' => now(),
                'visit_count' => 1,

                'country' => $geo['country'],
                'city' => $geo['city'],
                'asn' => $geo['asn'],
                'isp' => $geo['isp'],
                'device_type' => $deviceType,
                'os' => $os,
                'browser' => $browser,
            ]);
        }

        return $next($request);
    }

    protected function isBot($userAgent): bool
    {
        if (!$userAgent) {
            return false;
        }

        $userAgent = strtolower($userAgent);

        $botPatterns = [
            'googlebot',
            'bingbot',
            'yahoo',
            'duckduckbot',
            'baiduspider',
            'yandexbot',
            'applebot',
            'ahrefsbot',
            'mj12bot',
            'semrushbot',
            'siteauditbot',
            'bot',
            'spider',
            'crawl',
        ];

        foreach ($botPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }
}
