<?php

namespace CodeZero\Localizer;

use CodeZero\BrowserLocale\Laravel\BrowserLocaleServiceProvider;
use Illuminate\Support\ServiceProvider;

class LocalizerServiceProvider extends ServiceProvider
{
    /**
     * The package name.
     *
     * @var string
     */
    protected $name = 'localizer';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPublishableFiles();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
        $this->registerLocalizer();
        $this->registerProviders();
    }

    /**
     * Register the publishable files.
     *
     * @return void
     */
    protected function registerPublishableFiles()
    {
        $this->publishes([
            __DIR__."/../config/{$this->name}.php" => config_path("{$this->name}.php"),
        ], 'config');
    }

    /**
     * Merge published configuration file with
     * the original package configuration file.
     *
     * @return void
     */
    protected function mergeConfig()
    {
        $this->mergeConfigFrom(__DIR__."/../config/{$this->name}.php", $this->name);
    }

    /**
     * Register Localizer.
     *
     * @return void
     */
    protected function registerLocalizer()
    {
        $this->app->bind(Localizer::class, function ($app) {
            $locales = $app['config']->get("{$this->name}.supported_locales");
            $detectors = $app['config']->get("{$this->name}.detectors");
            $stores = $app['config']->get("{$this->name}.stores");
            $trustedDetectors = $app['config']->get("{$this->name}.trusted_detectors");

            return new Localizer($locales, $detectors, $stores, $trustedDetectors);
        });
    }

    /**
     * Registers the package dependencies
     *
     * @return void
     */
    protected function registerProviders()
    {
        $this->app->register(BrowserLocaleServiceProvider::class);
    }
}
