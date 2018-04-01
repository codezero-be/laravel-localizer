<?php

namespace CodeZero\Localizer\Detectors;

use App;

class AppDetector implements Detector
{
    /**
     * Detect the locale.
     *
     * @return string|array|null
     */
    public function detect()
    {
        return App::getLocale();
    }
}
