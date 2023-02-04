# Laravel Localizer

## IMPORTANT: March 2022

[![Support Ukraine](https://raw.githubusercontent.com/hampusborgos/country-flags/main/png100px/ua.png)](https://github.com/hampusborgos/country-flags/blob/main/png100px/ua.png)

It's horrible to see what is happening now in Ukraine, as Russian army is
[bombarding houses, hospitals and kindergartens](https://twitter.com/DavidCornDC/status/1501620037785997316).

Please [check out supportukrainenow.org](https://supportukrainenow.org/) for the ways how you can help people there.
Spread the word.

And if you are from Russia and you are against this war, please express your protest in some way.
I know you can get punished for this, but you are one of the hopes of those innocent people.

---

[![GitHub release](https://img.shields.io/github/release/codezero-be/laravel-localizer.svg?style=flat-square)](https://github.com/codezero-be/laravel-localizer/releases)
[![Laravel](https://img.shields.io/badge/laravel-10-red?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![License](https://img.shields.io/packagist/l/codezero/laravel-localizer.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/github/actions/workflow/status/codezero-be/laravel-localizer/run-tests.yml?style=flat-square&logo=github&logoColor=white&label=tests)](https://github.com/codezero-be/laravel-localizer/actions)
[![Code Coverage](https://img.shields.io/codacy/coverage/ad6fcea152b449d380a187a375d0f7d7/master?style=flat-square)](https://app.codacy.com/gh/codezero-be/laravel-localizer)
[![Code Quality](https://img.shields.io/codacy/grade/ad6fcea152b449d380a187a375d0f7d7/master?style=flat-square)](https://app.codacy.com/gh/codezero-be/laravel-localizer)
[![Total Downloads](https://img.shields.io/packagist/dt/codezero/laravel-localizer.svg?style=flat-square)](https://packagist.org/packages/codezero/laravel-localizer)

[![ko-fi](https://www.ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/R6R3UQ8V)

#### Automatically detect and set an app locale that matches your visitor's preference.

- Define your supported locales and match your visitor's preference
- Uses the most common locale [detectors](#detectors) by default
- Uses the most common locale [stores](#stores) by default
- Easily create and add your own detectors and stores

## Requirements

- PHP >= 7.1
- Laravel >= 5.6

## Install

```bash
composer require codezero/laravel-localizer
```

Laravel will automatically register the ServiceProvider.

#### Add Middleware

Add the middleware to the `web` middleware group in `app/Http/Kernel.php`, after `StartSession` and before `SubstituteBindings`:

```php
protected $middlewareGroups = [
    'web' => [
        \Illuminate\Session\Middleware\StartSession::class, // <= after this
        //...
        \CodeZero\Localizer\Middleware\SetLocale::class,
        //...
        \Illuminate\Routing\Middleware\SubstituteBindings::class, // <= before this
    ],
];
```

In Laravel 6.x you also need to add the middleware to the `$middlewarePriority` array in `app/Http/Kernel.php` to trigger it in the correct order:

```php
protected $middlewarePriority = [
    \Illuminate\Session\Middleware\StartSession::class, // <= after this
    //...
    \CodeZero\Localizer\Middleware\SetLocale::class,
    //...
    \Illuminate\Routing\Middleware\SubstituteBindings::class, // <= before this
];
```

#### Publish Configuration File

```bash
php artisan vendor:publish --provider="CodeZero\Localizer\LocalizerServiceProvider" --tag="config"
```

You will now find a `localizer.php` file in the `config` folder.

#### Configure Supported Locales

Add any locales you wish to support to your published `config/localizer.php` file:

```php
'supported-locales' => ['en', 'nl', 'fr'];
```

## Drivers

#### Detectors

By default the middleware will use the following detectors to check for a supported locale in:

1. The URL slug
2. The session
3. A cookie
4. The browser
5. The app's default locale

If you publish the configuration file, you can choose which detectors to run and in what order.

You can also create your own detector by implementing the `\CodeZero\Localizer\Detectors\Detector` interface and add a reference to it in the config file. The detectors are resolved from Laravel's IOC container, so you can add any dependencies to your constructor.

####  Stores

The first supported locale that is returned by a detector will then be stored in:

- The session
- A cookie
- The app locale

If you publish the configuration file, you can choose which stores to use.

You can also create your own store by implementing the `\CodeZero\Localizer\Stores\Store` interface and add a reference to it in the config file. The stores are resolved from Laravel's IOC container, so you can add any dependencies to your constructor.

## Testing

```
composer test
```

## Security

If you discover any security related issues, please [e-mail me](mailto:ivan@codezero.be) instead of using the issue tracker.

## Changelog

A complete list of all notable changes to this package can be found on the
[releases page](https://github.com/codezero-be/laravel-localizer/releases).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
