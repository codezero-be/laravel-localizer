<?php

namespace CodeZero\Localizer\Detectors;

use Config;
use Request;

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
