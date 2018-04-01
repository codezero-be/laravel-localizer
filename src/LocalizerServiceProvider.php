<?php

namespace CodeZero\Localizer;

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
            $locales = $app['config']->get("{$this->name}.supported-locales");
            $detectors = $app['config']->get("{$this->name}.detectors");
            $stores = $app['config']->get("{$this->name}.stores");

            return new Localizer($locales, $detectors, $stores);
        });
    }
}
