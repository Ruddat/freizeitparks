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
        // WENN die Route "/track-dwell-time" ist → NICHT tracken
        if ($request->is('track-dwell-time')) {
            return $next($request);
        }

        $referer = $request->header('referer');
        $landingPage = $request->fullUrl();
        $userAgent = $request->header('User-Agent') ?? '';
        $userId = auth()->id();
        $ipAddress = $request->ip();
        $isBot = $this->isBot($userAgent);

        // Quelle bestimmen
        $source = $this->determineSource($referer, $isBot);

        // Keyword und Sprache extrahieren
        $keywordData = $this->extractKeywordAndLanguage($request, $referer, $landingPage);
        $keyword = $keywordData['keyword'];
        $language = $keywordData['language'];

        if (!$keyword) {
            \Log::info('TrackReferral: Kein Keyword gefunden', [
                'referer' => $referer,
                'landingPage' => $landingPage,
            ]);
        }

        // Geo-Location-Daten holen
        $geo = $this->getGeoLocation($ipAddress);

        // Geräteinfos ermitteln
        $parser = new Parser($userAgent);
        $deviceType = $parser->device->type ?? null;
        $os = $parser->os->name ?? null;
        $browser = $parser->browser->name ?? null;

        // Bestehenden Log-Eintrag prüfen
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
                'keyword' => $keyword ?? 'unknown',
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
                'keyword_missing' => $keyword ? 0 : 1,
                'browser_language' => $language,
            ]);
        }

        return $next($request);
    }

    protected function isBot($userAgent): bool
    {
        if (!$userAgent) return false;

        $botPatterns = [
            'googlebot', 'bingbot', 'yahoo', 'duckduckbot', 'baiduspider',
            'yandexbot', 'applebot', 'ahrefsbot', 'mj12bot', 'semrushbot',
            'siteauditbot', 'bot', 'spider', 'crawl',
        ];

        $userAgent = strtolower($userAgent);

        foreach ($botPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    protected function determineSource(?string $referer, bool $isBot): string
    {
        if ($isBot) {
            return 'bot';
        }

        if ($referer) {
            $parsedReferer = parse_url($referer);
            $host = $parsedReferer['host'] ?? '';

            if (str_contains($host, 'google')) {
                return 'google';
            } elseif (str_contains($host, 'bing')) {
                return 'bing';
            } elseif (str_contains($host, 'yahoo')) {
                return 'yahoo';
            }
        }

        return 'direct';
    }

    protected function extractKeywordAndLanguage(Request $request, ?string $referer, string $landingPage): array
    {
        $keyword = null;
        $language = null;

        // Keyword aus Referer holen
        if ($referer && $parsedReferer = parse_url($referer)) {
            if (isset($parsedReferer['query'])) {
                parse_str($parsedReferer['query'], $params);
                $keyword = $params['q']
                    ?? $params['p']
                    ?? $params['query']
                    ?? $params['utm_term']
                    ?? $params['keyword']
                    ?? null;
            }

            if (!$keyword && isset($parsedReferer['path']) && str_contains($parsedReferer['path'], '/search')) {
                if (isset($parsedReferer['query'])) {
                    parse_str($parsedReferer['query'], $params);
                    $keyword = $params['q']
                        ?? $params['p']
                        ?? $params['query']
                        ?? null;
                }
            }
        }

        // Keyword aus Landing Page holen
        if (!$keyword && $landingQuery = parse_url($landingPage, PHP_URL_QUERY)) {
            parse_str($landingQuery, $landingParams);
            $keyword = $landingParams['q']
                ?? $landingParams['p']
                ?? $landingParams['query']
                ?? $landingParams['utm_term']
                ?? $landingParams['keyword']
                ?? null;
        }

        // Sprache holen
        $acceptLanguage = $request->header('Accept-Language');
        if ($acceptLanguage) {
            $language = explode(',', $acceptLanguage)[0];
        }

        return [
            'keyword' => $keyword ?: null,
            'language' => $language ?: null,
        ];
    }

    protected function getGeoLocation(string $ipAddress): array
    {
        $geo = [
            'country' => null,
            'city' => null,
            'asn' => null,
            'isp' => null,
        ];

        try {
            $cityPath = storage_path('app/geo/GeoLite2-City.mmdb');
            $asnPath = storage_path('app/geo/GeoLite2-ASN.mmdb');
            $countryPath = storage_path('app/geo/GeoLite2-Country.mmdb');

            if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE | FILTER_FLAG_NO_PRIV_RANGE)) {
                if (file_exists($cityPath)) {
                    $readerCity = new Reader($cityPath);
                    $recordCity = $readerCity->city($ipAddress);

                    $geo['country'] = $recordCity->country->name ?? null;
                    $geo['city'] = $recordCity->city->name ?? null;
                } elseif (file_exists($countryPath)) {
                    $readerCountry = new Reader($countryPath);
                    $recordCountry = $readerCountry->country($ipAddress);
                    $geo['country'] = $recordCountry->country->name ?? null;
                }

                if (file_exists($asnPath)) {
                    $readerASN = new Reader($asnPath);
                    $recordASN = $readerASN->asn($ipAddress);
                    $geo['asn'] = $recordASN->autonomousSystemNumber ?? null;
                    $geo['isp'] = $recordASN->autonomousSystemOrganization ?? null;
                }
            }
        } catch (\Exception $e) {
            \Log::warning('GeoIP Fehler: ' . $e->getMessage());
        }

        return $geo;
    }
}
