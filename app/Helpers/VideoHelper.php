<?php

if (!function_exists('getResponsiveYouTubeUrl')) {
    function getResponsiveYouTubeUrl($embedHtml): string
    {
        // src aus iframe-HTML extrahieren
        if (preg_match('/src="([^"]+)"/', $embedHtml, $matches)) {
            $url = $matches[1];

            // YouTube Embed URL erweitern um autoplay, mute etc.
            $url .= (str_contains($url, '?') ? '&' : '?') . 'autoplay=1&mute=1&controls=0&rel=0';

            return $url;
        }

        return '';
    }
}
