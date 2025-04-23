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
        $userAgent = $request->header('User-Agent') ?? '';
        $userId = auth()->id();

        // Lokale Fake-IP für Tests
        // $ipAddress = app()->environment('local') ? '8.8.8.8' : $request->ip();
        $ipAddress = $request->ip();

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
            $ip = $ipAddress;

            $cityPath = storage_path('app/geo/GeoLite2-City.mmdb');
            $asnPath = storage_path('app/geo/GeoLite2-ASN.mmdb');
            $countryPath = storage_path('app/geo/GeoLite2-Country.mmdb');

            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE | FILTER_FLAG_NO_PRIV_RANGE)) {
                if (file_exists($cityPath)) {
                    $readerCity = new Reader($cityPath);
                    $recordCity = $readerCity->city($ip);

                    $geo['country'] = $recordCity->country->name ?? $geo['country'];
                    $geo['city'] = $recordCity->city->name ?? null;
                } elseif (file_exists($countryPath)) {
                    $readerCountry = new Reader($countryPath);
                    $recordCountry = $readerCountry->country($ip);
                    $geo['country'] = $recordCountry->country->name ?? null;
                }

                if (file_exists($asnPath)) {
                    $readerASN = new Reader($asnPath);
                    $recordASN = $readerASN->asn($ip);
                    $geo['asn'] = $recordASN->autonomousSystemNumber ?? null;
                    $geo['isp'] = $recordASN->autonomousSystemOrganization ?? null;
                }
            }
        } catch (\Exception $e) {
            \Log::warning('GeoIP Fehler: ' . $e->getMessage());
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
}
