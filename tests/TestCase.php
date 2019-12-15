<?php

namespace CodeZero\Localizer\Tests;

use CodeZero\Localizer\LocalizerServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends  BaseTestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('app.key', Str::random(32));
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
