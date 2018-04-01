<?php

namespace CodeZero\Localizer\Tests;

use CodeZero\Localizer\LocalizerServiceProvider;
use Config;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends  BaseTestCase
{
    protected $localesKey;
    protected $sessionKey;
    protected $cookieName;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->localesKey = 'localizer.supported-locales';
        $this->sessionKey = Config::get('localizer.session-key');
        $this->cookieName = Config::get('localizer.cookie-name');

        Config::set('app.key', str_random(32));
    }

    /**
     * Get the packages service providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            LocalizerServiceProvider::class,
        ];
    }
}
