<?php

namespace CodeZero\Localizer\Detectors;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;

class UrlDetector implements Detector
{
    /**
     * Detect the locale.
     *
     * @return string|array|null
     */
    public function detect()
    {
        $position = Config::get('localizer.url-segment');

        return Request::segment($position);
    }
}
