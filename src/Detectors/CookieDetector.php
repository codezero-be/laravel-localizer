<?php

namespace CodeZero\Localizer\Detectors;

use Config;
use Cookie;

class CookieDetector implements Detector
{
    /**
     * Detect the locale.
     *
     * @return string|array|null
     */
    public function detect()
    {
        $key = Config::get('localizer.cookie-name');

        return Cookie::get($key);
    }
}
