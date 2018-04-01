<?php

namespace CodeZero\Localizer\Stores;

use Config;
use Cookie;

class CookieStore implements Store
{
    /**
     * Store the given locale.
     *
     * @param string $locale
     *
     * @return void
     */
    public function store($locale)
    {
        $name = Config::get('localizer.cookie-name');
        $minutes = Config::get('localizer.cookie-minutes');

        Cookie::queue($name, $locale, $minutes);
    }
}
