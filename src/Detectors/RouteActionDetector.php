<?php

namespace CodeZero\Localizer\Detectors;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class RouteActionDetector implements Detector
{
    /**
     * The current request.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Create a new Detector instance.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Detect the locale.
     *
     * @return string|array|null
     */
    public function detect()
    {
        $action = Config::get('localizer.route-action');

        return $this->request->route()->getAction($action);
    }
}
