<?php

namespace CodeZero\Localizer\Tests\Unit;

use CodeZero\Localizer\Detectors\Detector;
use CodeZero\Localizer\Localizer;
use CodeZero\Localizer\Stores\Store;
use CodeZero\Localizer\Tests\TestCase;
use Illuminate\Support\Facades\App;
use Mockery;

class LocalizerTest extends TestCase
{
    /** @test */
    public function it_loops_through_the_detectors_and_returns_the_first_supported_locale()
    {
        $supportedLocales = ['en', 'nl'];
        $detectors = [
            Mockery::mock(Detector::class)->allows()->detect()->andReturns('de')->getMock(),
            Mockery::mock(Detector::class)->allows()->detect()->andReturns('nl')->getMock(),
            Mockery::mock(Detector::class)->allows()->detect()->andReturns('en')->getMock(),
        ];

        $localizer = new Localizer($supportedLocales, $detectors);

        $this->assertEquals('nl', $localizer->detect());
    }

    /** @test */
    public function it_returns_the_best_match_if_an_array_of_locales_is_detected()
    {
        $supportedLocales = ['en', 'nl'];
        $detectors = [
            Mockery::mock(Detector::class)->allows()->detect()->andReturns(['de', 'nl', 'en'])->getMock(),
        ];

        $localizer = new Localizer($supportedLocales, $detectors);

        $this->assertEquals('nl', $localizer->detect());
    }

    /** @test */
    public function trusted_detectors_ignore_supported_locales_and_may_set_any_locale()
    {
        $supportedLocales = ['en'];
        $detectors = [
            Mockery::mock(Detector::class)->allows()->detect()->andReturns('nl')->getMock(),
        ];
        $trustedDetectors = [
            Detector::class,
        ];

        $localizer = new Localizer($supportedLocales, $detectors, [], $trustedDetectors);

        $this->assertEquals('nl', $localizer->detect());
    }

    /** @test */
    public function empty_locales_from_trusted_detectors_are_ignored()
    {
        $supportedLocales = ['en'];
        $detectors = [
            Mockery::mock(Detector::class)->allows()->detect()->andReturns(false)->getMock(),
            Mockery::mock(Detector::class)->allows()->detect()->andReturns(null)->getMock(),
            Mockery::mock(Detector::class)->allows()->detect()->andReturns([])->getMock(),
            Mockery::mock(Detector::class)->allows()->detect()->andReturns('')->getMock(),
            Mockery::mock(Detector::class)->allows()->detect()->andReturns('en')->getMock(),
        ];
        $trustedDetectors = [
            Detector::class,
        ];

        $localizer = new Localizer($supportedLocales, $detectors, [], $trustedDetectors);

        $this->assertEquals('en', $localizer->detect());
    }

    /** @test */
    public function it_returns_false_if_no_supported_locale_could_be_detected()
    {
        $supportedLocales = ['en'];
        $detectors = [
            Mockery::mock(Detector::class)->allows()->detect()->andReturns('de')->getMock(),
            Mockery::mock(Detector::class)->allows()->detect()->andReturns('nl')->getMock(),
            Mockery::mock(Detector::class)->allows()->detect()->andReturns('fr')->getMock(),
        ];

        $localizer = new Localizer($supportedLocales, $detectors);

        $this->assertFalse($localizer->detect());
    }

    /** @test */
    public function it_skips_null_and_false_and_empty_values()
    {
        $supportedLocales = ['nl'];
        $detectors = [
            Mockery::mock(Detector::class)->allows()->detect()->andReturns(false)->getMock(),
            Mockery::mock(Detector::class)->allows()->detect()->andReturns(null)->getMock(),
            Mockery::mock(Detector::class)->allows()->detect()->andReturns([])->getMock(),
            Mockery::mock(Detector::class)->allows()->detect()->andReturns('')->getMock(),
            Mockery::mock(Detector::class)->allows()->detect()->andReturns('nl')->getMock(),
        ];

        $localizer = new Localizer($supportedLocales, $detectors);

        $this->assertEquals('nl', $localizer->detect());
    }

    /** @test */
    public function it_loops_through_the_stores_and_calls_the_store_method_with_the_given_locale()
    {
        $stores = [
            Mockery::mock(Store::class)->expects()->store('nl')->once()->getMock(),
            Mockery::mock(Store::class)->expects()->store('nl')->once()->getMock(),
            Mockery::mock(Store::class)->expects()->store('nl')->once()->getMock(),
        ];

        $localizer = new Localizer([], [], $stores);

        $localizer->store('nl');
    }

    /** @test */
    public function it_accepts_class_names_instead_of_instances_in_the_constructor()
    {
        App::instance(Store::class, Mockery::mock(Store::class)->expects()->store('nl')->once()->getMock());
        App::instance(Detector::class, Mockery::mock(Detector::class)->expects()->detect()->once()->getMock());

        $detectors = [Detector::class];
        $stores = [Store::class];

        $localizer = new Localizer([], $detectors, $stores);

        $localizer->detect();
        $localizer->store('nl');
    }

    /** @test */
    public function you_can_set_the_supported_locales_at_runtime()
    {
        $supportedLocales = ['en'];
        $detectors = [
            Mockery::mock(Detector::class)->allows()->detect()->andReturns('en')->getMock(),
            Mockery::mock(Detector::class)->allows()->detect()->andReturns('nl')->getMock(),
        ];

        $localizer = new Localizer($supportedLocales, $detectors);
        $value = $localizer->setSupportedLocales(['nl']);

        $this->assertEquals('nl', $localizer->detect());
        $this->assertEquals($localizer, $value);
    }
}
