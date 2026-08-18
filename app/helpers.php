<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Read a site setting. Cached forever, busted on write.
     *
     * Only ever call this with keys that are safe to render. Security policy
     * values (OTP TTL, lockout windows) are read server-side in services,
     * never echoed into a Blade template.
     */
    function setting(string $key, ?string $default = null): ?string
    {
        return Setting::get($key, $default);
    }
}
