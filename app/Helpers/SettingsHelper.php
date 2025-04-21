<?php

use App\Models\ModSiteSettings;

if (!function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return ModSiteSettings::get($key, $default);
    }
}
