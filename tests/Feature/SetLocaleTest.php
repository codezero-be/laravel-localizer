<?php

namespace CodeZero\Localizer\Tests\Feature;

use CodeZero\BrowserLocale\BrowserLocale;
use CodeZero\Localizer\Middleware\SetLocale;
use CodeZero\Localizer\Tests\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class SetLocaleTest extends TestCase
{
    protected $sessionKey;
    protected $cookieName;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->sessionKey = Config::get('localizer.session-key');
        $this->cookieName = Config::get('localizer.cookie-name');
    }

    /** @test */
    public function it_looks_for_a_locale_in_the_url_first()
    {
        $this->setSupportedLocales(['en', 'nl', 'fr', 'de', 'es', 'it']);
        $this->setSessionLocale('fr');
        $this->setBrowserLocales('it');
        $this->setAppLocale('en');
        $this->setCarbonLocale('es');
        $cookie = 'de';

        Route::get('nl/some/route', function () {
            return App::getLocale();
        })->middleware(['web', SetLocale::class]);

        $response = $this->getWithCookie('nl/some/route', $cookie);

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    /** @test */
    public function you_can_configure_which_segment_to_use_as_locale()
    {
        $this->setSupportedLocales(['en', 'nl', 'fr', 'de', 'es', 'it']);
        $this->setSessionLocale('fr');
        $this->setBrowserLocales('it');
        $this->setAppLocale('en');
        $this->setCarbonLocale('es');
        $cookie = 'de';

        Config::set('localizer.url-segment', 2);

        Route::get('some/nl/route', function () {
            return App::getLocale();
        })->middleware(['web', SetLocale::class]);

        $response = $this->getWithCookie('some/nl/route', $cookie);

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    /** @test */
    public function it_looks_for_a_locale_in_the_session_if_not_found_in_the_url()
    {
        $this->setSupportedLocales(['en', 'nl', 'fr', 'de', 'es', 'it']);
        $this->setSessionLocale('fr');
        $this->setBrowserLocales('it');
        $this->setAppLocale('en');
        $this->setCarbonLocale('es');
        $cookie = 'de';

        Route::get('some/route', function () {
            return App::getLocale();
        })->middleware(['web', SetLocale::class]);

        $response = $this->getWithCookie('some/route', $cookie);

        $response->assertSessionHas($this->sessionKey, 'fr');
        $response->assertCookie($this->cookieName, 'fr');
        $this->assertEquals('fr', $response->original);
    }

    /** @test */
    public function it_looks_for_a_locale_in_a_cookie_if_not_found_in_the_url_or_session()
    {
        $this->setSupportedLocales(['en', 'nl', 'fr', 'de', 'es', 'it']);
        $this->setSessionLocale(null);
        $this->setBrowserLocales('it');
        $this->setAppLocale('en');
        $this->setCarbonLocale('es');
        $cookie = 'de';

        Route::get('some/route', function () {
            return App::getLocale();
        })->middleware(['web', SetLocale::class]);

        $response = $this->getWithCookie('some/route', $cookie);

        $response->assertSessionHas($this->sessionKey, 'de');
        $response->assertCookie($this->cookieName, 'de');
        $this->assertEquals('de', $response->original);
    }

    /** @test */
    public function it_looks_for_a_locale_in_the_browser_if_not_found_in_the_url_or_session_or_cookie()
    {
        $this->setSupportedLocales(['en', 'nl', 'fr', 'de', 'es', 'it']);
        $this->setSessionLocale(null);
        $this->setBrowserLocales('it');
        $this->setAppLocale('en');
        $this->setCarbonLocale('es');

        Route::get('some/route', function () {
            return App::getLocale();
        })->middleware(['web', SetLocale::class]);

        $response = $this->get('some/route');

        $response->assertSessionHas($this->sessionKey, 'it');
        $response->assertCookie($this->cookieName, 'it');
        $this->assertEquals('it', $response->original);
    }

    /** @test */
    public function it_returns_the_best_match_when_a_browser_locale_is_used()
    {
        $this->setSupportedLocales(['en', 'nl', 'fr', 'de', 'es', 'it']);
        $this->setSessionLocale(null);
        $this->setBrowserLocales('cs,it-IT;q=0.4,es;q=0.8');
        $this->setAppLocale('en');
        $this->setCarbonLocale('es');

        Route::get('some/route', function () {
            return App::getLocale();
        })->middleware(['web', SetLocale::class]);

        $response = $this->get('some/route');

        $response->assertSessionHas($this->sessionKey, 'es');
        $response->assertCookie($this->cookieName, 'es');
        $this->assertEquals('es', $response->original);
    }

    /** @test */
    public function it_defaults_to_the_current_app_locale()
    {
        $this->setSupportedLocales(['en', 'nl', 'fr', 'de', 'es', 'it']);
        $this->setSessionLocale(null);
        $this->setBrowserLocales(null);
        $this->setAppLocale('en');
        $this->setCarbonLocale('es');

        Route::get('some/route', function () {
            return App::getLocale();
        })->middleware(['web', SetLocale::class]);

        $response = $this->get('some/route');

        $response->assertSessionHas($this->sessionKey, 'en');
        $response->assertCookie($this->cookieName, 'en');
        $this->assertEquals('en', $response->original);
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
     * Set the current carbon locale.
     *
     * @param string $locale
     *
     * @return $this
     */
    protected function setCarbonLocale($locale)
    {
        Carbon::setLocale($locale);

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
        Config::set('localizer.supported-locales', $locales);

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
     * Perform a GET request when the given cookie was previously set.
     *
     * @param string $url
     * @param string $cookie
     *
     * @return \Illuminate\Testing\TestResponse
     */
    protected function getWithCookie($url, $cookie)
    {
        return App::version() < 6
            ? $this->call('GET', $url, [], [$this->cookieName => Crypt::encrypt($cookie, false)])
            : $this->withCookie($this->cookieName, $cookie)->get($url);
    }
}
