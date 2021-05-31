<?php

namespace CodeZero\Localizer\Stores;

use Illuminate\Support\Carbon;

class CarbonStore implements Store
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
        Carbon::setLocale($locale);
    }
}
