<?php

namespace CodeZero\Localizer\Detectors;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class UserDetector implements Detector
{
    /**
     * Detect the locale.
     *
     * @return string|array|null
     */
    public function detect()
    {
        $user = Auth::user();

        if ($user === null) {
            return null;
        }

        $attribute = Config::get('localizer.user_attribute');

        return $user->getAttributeValue($attribute);
    }
}
