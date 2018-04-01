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
        $locales = ['en', 'nl'];
        $detectors = [
            Mockery::mock(Detector::class)->allows()->detect()->andReturns(false)->getMock(),
            Mockery::mock(Detector::class)->allows()->detect()->andReturns(null)->getMock(),
            Mockery::mock(Detector::class)->allows()->detect()->andReturns('de')->getMock(),
            Mockery::mock(Detector::class)->allows()->detect()->andReturns('nl')->getMock(),
            Mockery::mock(Detector::class)->allows()->detect()->andReturns('en')->getMock(),
        ];

        $localizer = new Localizer($locales, $detectors);

        $this->assertEquals('nl', $localizer->detect());
    }

    /** @test */
    public function it_returns_the_best_match_if_an_array_of_locales_is_detected()
    {
        $locales = ['en', 'nl'];
        $detectors = [
            Mockery::mock(Detector::class)->allows()->detect()->andReturns(['de', 'nl', 'en'])->getMock(),
        ];

        $localizer = new Localizer($locales, $detectors);

        $this->assertEquals('nl', $localizer->detect());
    }

    /** @test */
    public function it_returns_false_if_no_supported_locale_could_be_detected()
    {
        $locales = ['en'];
        $detectors = [
            Mockery::mock(Detector::class)->allows()->detect()->andReturns(false)->getMock(),
            Mockery::mock(Detector::class)->allows()->detect()->andReturns(null)->getMock(),
            Mockery::mock(Detector::class)->allows()->detect()->andReturns('de')->getMock(),
            Mockery::mock(Detector::class)->allows()->detect()->andReturns('nl')->getMock(),
            Mockery::mock(Detector::class)->allows()->detect()->andReturns('fr')->getMock(),
        ];

        $localizer = new Localizer($locales, $detectors);

        $this->assertFalse($localizer->detect());
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
}
