<?php

namespace CodeZero\Localizer\Tests\Feature;

use App;
use CodeZero\Localizer\Middleware\SetLocale;
use CodeZero\Localizer\Tests\TestCase;
use CodeZero\BrowserLocale\BrowserLocale;
use Config;
use Crypt;
use Illuminate\Foundation\Testing\TestResponse;
use Route;
use Session;

class SetLocaleTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        TestResponse::macro('assertAppLocale', function ($locale) {
            return $this->assertSee('The locale is: ' . $locale);
        });

        $this->withoutExceptionHandling();
        $this->setSupportedLocales(['en', 'nl', 'fr', 'de', 'es', 'it']);
    }

    /** @test */
    public function it_looks_for_a_locale_in_the_url_first()
    {
        $this->registerRoute('nl/some/route');

        $this->setSessionLocale('fr');
        $this->setBrowserLocales('it');
        $this->setAppLocale('en');

        $cookie = [$this->cookieName => Crypt::encrypt('de')];

        $this->call('GET', 'nl/some/route', [], $cookie)
            ->assertSessionHas($this->sessionKey, 'nl')
            ->assertCookie($this->cookieName, 'nl')
            ->assertAppLocale('nl');
    }

    /** @test */
    public function you_can_configure_which_segment_to_use_as_locale()
    {
        $this->registerRoute('some/nl/route');

        Config::set('localizer.url-segment', 2);

        $this->setSessionLocale('fr');
        $this->setBrowserLocales('it');
        $this->setAppLocale('en');

        $cookie = [$this->cookieName => Crypt::encrypt('de')];

        $this->call('GET', 'some/nl/route', [], $cookie)
            ->assertSessionHas($this->sessionKey, 'nl')
            ->assertCookie($this->cookieName, 'nl')
            ->assertAppLocale('nl');
    }

    /** @test */
    public function it_looks_for_a_locale_in_the_session_if_not_found_in_the_url()
    {
        $this->registerRoute('some/route');

        $this->setSessionLocale('fr');
        $this->setBrowserLocales('it');
        $this->setAppLocale('en');

        $cookie = [$this->cookieName => Crypt::encrypt('de')];

        $this->call('GET', 'some/route', [], $cookie)
            ->assertSessionHas($this->sessionKey, 'fr')
            ->assertCookie($this->cookieName, 'fr')
            ->assertAppLocale('fr');
    }

    /** @test */
    public function it_looks_for_a_locale_in_a_cookie_if_not_found_in_the_url_or_session()
    {
        $this->registerRoute('some/route');

        $this->setSessionLocale(null);
        $this->setBrowserLocales('it');
        $this->setAppLocale('en');

        $cookie = [$this->cookieName => Crypt::encrypt('de')];

        $this->call('GET', 'some/route', [], $cookie)
            ->assertSessionHas($this->sessionKey, 'de')
            ->assertCookie($this->cookieName, 'de')
            ->assertAppLocale('de');
    }

    /** @test */
    public function it_looks_for_a_locale_in_the_browser_if_not_found_in_the_url_or_session_or_cookie()
    {
        $this->registerRoute('some/route');

        $this->setSessionLocale(null);
        $this->setBrowserLocales('it');
        $this->setAppLocale('en');

        $cookie = [];

        $this->call('GET', 'some/route', [], $cookie)
            ->assertSessionHas($this->sessionKey, 'it')
            ->assertCookie($this->cookieName, 'it')
            ->assertAppLocale('it');
    }

    /** @test */
    public function it_returns_the_best_match_when_a_browser_locale_is_used()
    {
        $this->registerRoute('some/route');

        $this->setSessionLocale(null);
        $this->setBrowserLocales('cs,it-IT;q=0.8,es;q=0.4');
        $this->setAppLocale('en');

        $cookie = [];

        $this->call('GET', 'some/route', [], $cookie)
            ->assertSessionHas($this->sessionKey, 'it')
            ->assertCookie($this->cookieName, 'it')
            ->assertAppLocale('it');
    }

    /** @test */
    public function it_defaults_to_the_current_app_locale()
    {
        $this->registerRoute('some/route');

        $this->setSessionLocale(null);
        $this->setBrowserLocales(null);
        $this->setAppLocale('en');

        $cookie = [];

        $this->call('GET', 'some/route', [], $cookie)
            ->assertSessionHas($this->sessionKey, 'en')
            ->assertCookie($this->cookieName, 'en')
            ->assertAppLocale('en');
    }

    /**
     * Set the current app locale.
     *
     * @param string $locale
     *
     * @return $this
     */
    protected function setAppLocale($locale)
    {
        App::setLocale($locale);

        return $this;
    }

    /**
     * Set the supported locales.
     *
     * @param array $locales
     *
     * @return $this
     */
    protected function setSupportedLocales(array $locales)
    {
        Config::set($this->localesKey, $locales);

        return $this;
    }

    /**
     * Set the locale in the session.
     *
     * @param string $locale
     *
     * @return $this
     */
    protected function setSessionLocale($locale)
    {
        Session::put($this->sessionKey, $locale);

        return $this;
    }

    /**
     * Set the locales used by the browser detector.
     *
     * @param string $locales
     *
     * @return $this
     */
    protected function setBrowserLocales($locales)
    {
        App::bind(BrowserLocale::class, function () use ($locales) {
            return new BrowserLocale($locales);
        });

        return $this;
    }

    /**
     * Register a route.
     *
     * @param string $url
     *
     * @return $this
     */
    protected function registerRoute($url)
    {
        Route::getRoutes()->add(
            Route::get($url, function () {
                return 'The locale is: ' . App::getLocale();
            })->middleware(['web', SetLocale::class])
        );

        return $this;
    }
}
