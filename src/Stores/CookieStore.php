<?php

namespace CodeZero\Localizer\Stores;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;

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
        $name = Config::get('localizer.cookie_name');
        $minutes = Config::get('localizer.cookie_minutes');

        Cookie::queue($name, $locale, $minutes);
    }
}
