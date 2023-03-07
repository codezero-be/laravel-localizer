<?php

namespace CodeZero\Localizer\Tests\Feature;

use CodeZero\BrowserLocale\BrowserLocale;
use CodeZero\Localizer\Middleware\SetLocale;
use CodeZero\Localizer\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
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
    public function it_looks_for_a_locale_in_a_custom_route_action()
    {
        $this->setSupportedLocales(['en', 'nl', 'fr', 'de', 'es', 'it']);
        $this->setSessionLocale('fr');
        $this->setBrowserLocales('it');
        $this->setAppLocale('en');
        $cookie = 'de';

        Route::group([
            'locale' => 'nl',
        ], function () {
            Route::get('some/route', function () {
                return App::getLocale();
            })->middleware(['web', SetLocale::class]);
        });

        $response = $this->getWithCookie('some/route', $cookie);

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    /** @test */
    public function it_looks_for_a_locale_in_the_url()
    {
        $this->setSupportedLocales(['en', 'nl', 'fr', 'de', 'es', 'it']);
        $this->setSessionLocale('fr');
        $this->setBrowserLocales('it');
        $this->setAppLocale('en');
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
    public function it_looks_for_custom_slugs()
    {
        $this->setSupportedLocales([
            'en' => 'english',
            'nl' => 'dutch',
            'fr' => 'french',
        ]);
        $this->setAppLocale('en');

        Route::get('dutch/some/route', function () {
            return App::getLocale();
        })->middleware(['web', SetLocale::class]);

        $response = $this->get('dutch/some/route');

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    /** @test */
    public function you_can_use_multiple_slugs_for_a_locale()
    {
        $this->setSupportedLocales([
            'en' => 'english',
            'nl' => ['dutch', 'nederlands'],
            'fr' => 'french',
        ]);
        $this->setAppLocale('en');

        Route::get('dutch/some/route', function () {
            return App::getLocale();
        })->middleware(['web', SetLocale::class]);

        Route::get('nederlands/some/route', function () {
            return App::getLocale();
        })->middleware(['web', SetLocale::class]);

        $response = $this->get('dutch/some/route');

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);

        $response = $this->get('nederlands/some/route');

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    /** @test */
    public function it_looks_for_custom_domains()
    {
        $this->setSupportedLocales([
            'en' => 'english.test',
            'nl' => 'dutch.test',
            'fr' => 'french.test',
        ]);
        $this->setAppLocale('en');

        Route::group([
            'domain' => 'dutch.test',
        ], function () {
            Route::get('some/route', function () {
                return App::getLocale();
            })->middleware(['web', SetLocale::class]);
        });

        $response = $this->get('http://dutch.test/some/route');

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    /** @test */
    public function you_can_use_multiple_domains_for_a_locale()
    {
        $this->setSupportedLocales([
            'en' => 'english.test',
            'nl' => ['dutch.test', 'nederlands.test'],
            'fr' => 'french.test',
        ]);
        $this->setAppLocale('en');

        Route::group([
            'domain' => 'dutch.test',
        ], function () {
            Route::get('some/route', function () {
                return App::getLocale();
            })->middleware(['web', SetLocale::class]);
        });

        Route::group([
            'domain' => 'nederlands.test',
        ], function () {
            Route::get('some/route', function () {
                return App::getLocale();
            })->middleware(['web', SetLocale::class]);
        });

        $response = $this->get('http://dutch.test/some/route');

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);

        $response = $this->get('http://nederlands.test/some/route');

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    /** @test */
    public function it_checks_for_a_configured_omitted_locale()
    {
        $this->setSupportedLocales(['en', 'nl', 'fr', 'de', 'es', 'it']);
        $this->setOmittedLocale('nl');
        $this->setSessionLocale('fr');
        $this->setBrowserLocales('it');
        $this->setAppLocale('en');
        $cookie = 'de';

        Route::get('some/route', function () {
            return App::getLocale();
        })->middleware(['web', SetLocale::class]);

        $response = $this->getWithCookie('some/route', $cookie);

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    /** @test */
    public function it_looks_for_a_locale_on_the_authenticated_user_if_not_found_in_the_url()
    {
        $this->setSupportedLocales(['en', 'nl', 'fr', 'de', 'es', 'it']);
        $this->setSessionLocale('fr');
        $this->setBrowserLocales('it');
        $this->setAppLocale('en');
        $cookie = 'de';

        $attribute = Config::get('localizer.user-attribute');
        $user = new User();
        $user->$attribute = 'nl';

        Route::get('some/route', function () {
            return App::getLocale();
        })->middleware(['web', SetLocale::class]);

        $response = $this->actingAs($user)->getWithCookie('some/route', $cookie);

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    /** @test */
    public function it_will_bypass_missing_attribute_exception_if_the_locale_attribute_is_missing_on_the_user_model()
    {
        if (version_compare(App::version(), '9.35.0') === -1) {
            $this->markTestSkipped('This test only applies to Laravel 9 and higher.');
        }

        $this->setSupportedLocales(['en', 'nl', 'fr', 'de', 'es', 'it']);
        $this->setSessionLocale('fr');
        $this->setBrowserLocales('it');
        $this->setAppLocale('en');
        $cookie = 'de';

        $user = new User();
        $user->exists = true;
        Model::preventAccessingMissingAttributes();

        Route::get('some/route', function () {
            return App::getLocale();
        })->middleware(['web', SetLocale::class]);

        $response = $this->actingAs($user)->getWithCookie('some/route', $cookie);

        $response->assertSessionHas($this->sessionKey, 'fr');
        $response->assertCookie($this->cookieName, 'fr');
        $this->assertEquals('fr', $response->original);
    }

    /** @test */
    public function it_looks_for_a_locale_in_the_session_if_not_found_in_the_url()
    {
        $this->setSupportedLocales(['en', 'nl', 'fr', 'de', 'es', 'it']);
        $this->setSessionLocale('fr');
        $this->setBrowserLocales('it');
        $this->setAppLocale('en');
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

        Route::get('some/route', function () {
            return App::getLocale();
        })->middleware(['web', SetLocale::class]);

        $response = $this->get('some/route');

        $response->assertSessionHas($this->sessionKey, 'en');
        $response->assertCookie($this->cookieName, 'en');
        $this->assertEquals('en', $response->original);
    }

    /** @test */
    public function trusted_detectors_ignore_supported_locales_and_may_set_any_locale()
    {
        $this->setSupportedLocales(['en']);
        $this->setAppLocale('en');

        $routeAction = ['locale' => 'nl'];

        Config::set('localizer.trusted-detectors', [
            \CodeZero\Localizer\Detectors\RouteActionDetector::class,
        ]);

        Route::group($routeAction, function () {
            Route::get('some/route', function () {
                return App::getLocale();
            })->middleware(['web', SetLocale::class]);
        });

        $response = $this->get('some/route');

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
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
        Config::set('localizer.supported-locales', $locales);

        return $this;
    }

    /**
     * Set the omitted locale.
     *
     * @param string $locale
     *
     * @return $this
     */
    protected function setOmittedLocale($locale)
    {
        Config::set('localizer.omitted-locale', $locale);

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
        return version_compare(App::version(), '6.0.0') === -1
            ? $this->call('GET', $url, [], [$this->cookieName => Crypt::encrypt($cookie, false)])
            : $this->withCookie($this->cookieName, $cookie)->get($url);
    }
}
