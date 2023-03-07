<?php

namespace CodeZero\Localizer\Detectors;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;

class RouteActionDetector implements Detector
{
    /**
     * Detect the locale.
     *
     * @return string|array|null
     */
    public function detect()
    {
        $action = Config::get('localizer.route-action');

        return Request::route()->getAction($action);
    }
}
