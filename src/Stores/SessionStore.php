<?php

namespace CodeZero\Localizer\Stores;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

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
        $key = Config::get('localizer.session_key');

        Session::put($key, $locale);
    }
}
