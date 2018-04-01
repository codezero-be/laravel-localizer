<?php

namespace CodeZero\Localizer\Detectors;

interface Detector
{
    /**
     * Detect the locale.
     *
     * @return string|array|null
     */
    public function detect();
}
