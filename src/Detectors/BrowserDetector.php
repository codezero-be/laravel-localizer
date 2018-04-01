<?php

namespace CodeZero\Localizer\Detectors;

use App;
use CodeZero\BrowserLocale\BrowserLocale;
use CodeZero\BrowserLocale\Filters\CombinedFilter;

class BrowserDetector implements Detector
{
    /**
     * Detect the locale.
     *
     * @return string|array|null
     */
    public function detect()
    {
        return App::make(BrowserLocale::class)->filter(new CombinedFilter);
    }
}
