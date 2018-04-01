<?php

namespace CodeZero\Localizer\Detectors;

use Config;
use Session;

class SessionDetector implements Detector
{
    /**
     * Detect the locale.
     *
     * @return string|array|null
     */
    public function detect()
    {
        $key = Config::get('localizer.session-key');

        return Session::get($key);
    }
}
