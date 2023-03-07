<?php

namespace CodeZero\Localizer;

use Illuminate\Support\Facades\App;

class Localizer
{
    /**
     * Supported locales.
     *
     * @var \Illuminate\Support\Collection|array
     */
    protected $locales;

    /**
     * \CoderZero\Localizer\Detectors\Detector class names or instances.
     *
     * @var \Illuminate\Support\Collection|array
     */
    protected $detectors;

    /**
     * \CoderZero\Localizer\Stores\Store instances.
     *
     * @var \Illuminate\Support\Collection|array
     */
    protected $stores;

    /**
     * \CoderZero\Localizer\Detectors\Detector class names.
     *
     * @var \Illuminate\Support\Collection|array
     */
    protected $trustedDetectors;

    /**
     * Create a new Localizer instance.
     *
     * @param \Illuminate\Support\Collection|array $locales
     * @param \Illuminate\Support\Collection|array $detectors
     * @param \Illuminate\Support\Collection|array $stores
     * @param \Illuminate\Support\Collection|array $trustedDetectors
     */
    public function __construct($locales, $detectors, $stores = [], $trustedDetectors = [])
    {
        $this->setSupportedLocales($locales);
        $this->detectors = $detectors;
        $this->stores = $stores;
        $this->trustedDetectors = $trustedDetectors;
    }

    /**
     * Detect any supported locale and return the first match.
     *
     * @return string|false
     */
    public function detect()
    {
        foreach ($this->detectors as $detector) {
            $locales = (array) $this->getInstance($detector)->detect();

            foreach ($locales as $locale) {
                if ($locale && ($this->isSupportedLocale($locale) || $this->isTrustedDetector($detector))) {
                    return $locale;
                }
            }
        }

        return false;
    }

    /**
     * Store the given locale.
     *
     * @param string $locale
     *
     * @return void
     */
    public function store($locale)
    {
        foreach ($this->stores as $store) {
            $this->getInstance($store)->store($locale);
        }
    }

    /**
     * Set the supported locales.
     *
     * @param array $locales
     *
     * @return \CodeZero\Localizer\Localizer
     */
    public function setSupportedLocales(array $locales)
    {
        if ( ! array_key_exists(0, $locales)) {
            $locales = array_keys($locales);
        }

        $this->locales = $locales;

        return $this;
    }

    /**
     * Check if the given locale is supported.
     *
     * @param mixed $locale
     *
     * @return bool
     */
    protected function isSupportedLocale($locale)
    {
        return in_array($locale, $this->locales);
    }

    /**
     * Check if the given Detector class is trusted.
     *
     * @param \CodeZero\Localizer\Detectors\Detector|string $detector
     *
     * @return bool
     */
    protected function isTrustedDetector($detector)
    {
        if (is_string($detector)) {
            return in_array($detector, $this->trustedDetectors);
        }

        foreach ($this->trustedDetectors as $trustedDetector) {
            if ($detector instanceof $trustedDetector) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the class from Laravel's IOC container if it is a string.
     *
     * @param mixed $class
     *
     * @return mixed
     */
    protected function getInstance($class)
    {
        if (is_string($class)) {
            return App::make($class);
        }

        return $class;
    }
}
