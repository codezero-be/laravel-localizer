<?php

namespace CodeZero\Localizer\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use CodeZero\BrowserLocale\BrowserLocale;
use CodeZero\Localizer\Middleware\SetLocale;
use CodeZero\Localizer\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

final class SetLocaleTest extends TestCase
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

        // Remove any default browser locales
        $this->setBrowserLocales(null);

        $this->sessionKey = Config::get('localizer.session_key');
        $this->cookieName = Config::get('localizer.cookie_name');
    }

    #[Test]
    public function it_looks_for_a_locale_in_a_custom_route_action(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        $routeAction = ['locale' => 'nl'];

        Route::group($routeAction, function () {
            Route::get('some/route', function () {
                return App::getLocale();
            })->middleware(['web']);
        });

        $response = $this->get('some/route');

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    #[Test]
    public function it_looks_for_a_locale_in_the_url(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        Route::get('nl/some/route', function () {
            return App::getLocale();
        })->middleware(['web']);

        $response = $this->get('nl/some/route');

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    #[Test]
    public function you_can_configure_which_segment_to_use_as_locale(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        Config::set('localizer.url_segment', 2);

        Route::get('some/nl/route', function () {
            return App::getLocale();
        })->middleware(['web']);

        $response = $this->get('some/nl/route');

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    #[Test]
    public function it_looks_for_custom_slugs(): void
    {
        $this->setSupportedLocales([
            'en' => 'english',
            'nl' => 'dutch',
        ]);
        $this->setAppLocale('en');

        Route::get('dutch/some/route', function () {
            return App::getLocale();
        })->middleware(['web']);

        $response = $this->get('dutch/some/route');

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    #[Test]
    public function you_can_use_multiple_slugs_for_a_locale(): void
    {
        $this->setSupportedLocales([
            'en' => 'english',
            'nl' => ['dutch', 'nederlands'],
        ]);
        $this->setAppLocale('en');

        Route::get('dutch/some/route', function () {
            return App::getLocale();
        })->middleware(['web']);

        Route::get('nederlands/some/route', function () {
            return App::getLocale();
        })->middleware(['web']);

        $response = $this->get('dutch/some/route');

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);

        $response = $this->get('nederlands/some/route');

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    #[Test]
    public function it_looks_for_custom_domains(): void
    {
        $this->setSupportedLocales([
            'en' => 'english.test',
            'nl' => 'dutch.test',
        ]);
        $this->setAppLocale('en');

        Route::group(['domain' => 'dutch.test'], function () {
            Route::get('some/route', function () {
                return App::getLocale();
            })->middleware(['web']);
        });

        $response = $this->get('http://dutch.test/some/route');

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    #[Test]
    public function you_can_use_multiple_domains_for_a_locale(): void
    {
        $this->setSupportedLocales([
            'en' => 'english.test',
            'nl' => ['dutch.test', 'nederlands.test'],
        ]);
        $this->setAppLocale('en');

        Route::group(['domain' => 'dutch.test'], function () {
            Route::get('some/route', function () {
                return App::getLocale();
            })->middleware(['web']);
        });

        Route::group(['domain' => 'nederlands.test'], function () {
            Route::get('some/route', function () {
                return App::getLocale();
            })->middleware(['web']);
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

    #[Test]
    public function it_checks_for_a_configured_omitted_locale(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        $this->setOmittedLocale('nl');

        Route::get('some/route', function () {
            return App::getLocale();
        })->middleware(['web']);

        $response = $this->get('some/route');

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    #[Test]
    public function it_looks_for_a_locale_on_the_authenticated_user(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        $attribute = Config::get('localizer.user_attribute');
        $user = new User();
        $user->$attribute = 'nl';

        Route::get('some/route', function () {
            return App::getLocale();
        })->middleware(['web']);

        $response = $this->actingAs($user)->get('some/route');

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    #[Test]
    public function it_will_bypass_missing_attribute_exception_if_the_locale_attribute_is_missing_on_the_user_model(): void
    {
        if (version_compare(App::version(), '9.35.0') === -1) {
            $this->markTestSkipped('This test only applies to Laravel 9.35.0 and higher.');
        }

        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        $user = new User();
        $user->exists = true; // exception is only thrown if user "exists"
        Model::preventAccessingMissingAttributes();

        Route::get('some/route', function () {
            return App::getLocale();
        })->middleware(['web']);

        $response = $this->actingAs($user)->get('some/route');

        $response->assertSessionHas($this->sessionKey, 'en');
        $response->assertCookie($this->cookieName, 'en');
        $this->assertEquals('en', $response->original);
    }

    #[Test]
    public function it_looks_for_a_locale_in_the_session(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        $this->setSessionLocale('nl');

        Route::get('some/route', function () {
            return App::getLocale();
        })->middleware(['web']);

        $response = $this->get('some/route');

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    #[Test]
    public function it_looks_for_a_locale_in_a_cookie(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        $cookie = 'nl';

        Route::get('some/route', function () {
            return App::getLocale();
        })->middleware(['web']);

        $response = $this->withCookie($this->cookieName, $cookie)
            ->get('some/route');

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    #[Test]
    public function it_looks_for_a_locale_in_the_browser(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        $this->setBrowserLocales('nl');

        Route::get('some/route', function () {
            return App::getLocale();
        })->middleware(['web']);

        $response = $this->get('some/route');

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    #[Test]
    public function it_returns_the_best_match_when_a_browser_locale_is_used(): void
    {
        $this->setSupportedLocales(['en', 'nl', 'fr']);
        $this->setAppLocale('en');

        $this->setBrowserLocales('de,fr;q=0.4,nl-BE;q=0.8');

        Route::get('some/route', function () {
            return App::getLocale();
        })->middleware(['web']);

        $response = $this->get('some/route');

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    #[Test]
    public function it_looks_for_the_current_app_locale(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('nl');

        Route::get('some/route', function () {
            return App::getLocale();
        })->middleware(['web']);

        $response = $this->get('some/route');

        $response->assertSessionHas($this->sessionKey, 'nl');
        $response->assertCookie($this->cookieName, 'nl');
        $this->assertEquals('nl', $response->original);
    }

    #[Test]
    public function trusted_detectors_ignore_supported_locales_and_may_set_any_locale(): void
    {
        $this->setSupportedLocales(['en']);
        $this->setAppLocale('en');

        $routeAction = ['locale' => 'nl'];

        Config::set('localizer.trusted_detectors', [
            \CodeZero\Localizer\Detectors\RouteActionDetector::class,
        ]);

        Route::group($routeAction, function () {
            Route::get('some/route', function () {
                return App::getLocale();
            })->middleware(['web']);
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
        Config::set('localizer.supported_locales', $locales);

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
        Config::set('localizer.omitted_locale', $locale);

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
}
