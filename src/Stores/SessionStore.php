<?php

namespace CodeZero\Localizer\Stores;

use Config;
use Session;

class SessionStore implements Store
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
        $key = Config::get('localizer.session-key');

        Session::put($key, $locale);
    }
}
