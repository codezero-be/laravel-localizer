<?php

namespace CodeZero\Localizer\Detectors;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class SessionDetector implements Detector
{
    /**
     * Detect the locale.
     *
     * @return string|array|null
     */
    public function detect()
    {
        $key = Config::get('localizer.session_key');

        return Session::get($key);
    }
}
