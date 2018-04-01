<?php

namespace CodeZero\Localizer\Stores;

use App;

class AppStore implements Store
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
        App::setLocale($locale);
    }
}
