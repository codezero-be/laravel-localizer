<?php

namespace CodeZero\Localizer\Detectors;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;

class UrlDetector implements Detector
{
    /**
     * Detect the locale.
     *
     * @return string|array|null
     */
    public function detect()
    {
        $locales = Config::get('localizer.supported-locales');
        $position = Config::get('localizer.url-segment');
        $slug = Request::segment($position);

        // If supported locales is a simple array like ['en', 'nl']
        // just return the slug and let Localizer check if it is supported.
        if (array_key_exists(0, $locales)) {
            return $slug;
        }

        // Find the locale that belongs to the custom domain or slug.
        $domain = Request::getHttpHost();
        $locales = $this->flipLocalesArray($locales);
        $locale = $locales[$domain] ?? $locales[$slug] ?? null;

        return $locale;
    }

    /**
     * Flip the locales array so the custom domain or slug
     * become the key and the locale becomes te value.
     *
     * @param array $locales
     *
     * @return array
     */
    protected function flipLocalesArray($locales)
    {
        $flipped = [];

        foreach ($locales as $locale => $values) {
            $values = is_array($values) ? $values : [$values];

            foreach ($values as $value) {
                $flipped[$value] = $locale;
            }
        }

        return $flipped;
    }
}
