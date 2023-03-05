<?php

namespace CodeZero\Localizer\Detectors;

use Illuminate\Support\Facades\Config;

class OmittedLocaleDetector implements Detector
{
    /**
     * Detect the locale.
     *
     * @return string|array|null
     */
    public function detect()
    {
        return Config::get('localizer.omitted-locale') ?: null;
    }
}
