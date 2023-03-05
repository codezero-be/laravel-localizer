# Laravel Localizer

[![GitHub release](https://img.shields.io/github/release/codezero-be/laravel-localizer.svg?style=flat-square)](https://github.com/codezero-be/laravel-localizer/releases)
[![Laravel](https://img.shields.io/badge/laravel-10-red?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![License](https://img.shields.io/packagist/l/codezero/laravel-localizer.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/github/actions/workflow/status/codezero-be/laravel-localizer/run-tests.yml?style=flat-square&logo=github&logoColor=white&label=tests)](https://github.com/codezero-be/laravel-localizer/actions)
[![Code Coverage](https://img.shields.io/codacy/coverage/ad6fcea152b449d380a187a375d0f7d7/master?style=flat-square)](https://app.codacy.com/gh/codezero-be/laravel-localizer)
[![Code Quality](https://img.shields.io/codacy/grade/ad6fcea152b449d380a187a375d0f7d7/master?style=flat-square)](https://app.codacy.com/gh/codezero-be/laravel-localizer)
[![Total Downloads](https://img.shields.io/packagist/dt/codezero/laravel-localizer.svg?style=flat-square)](https://packagist.org/packages/codezero/laravel-localizer)

[![ko-fi](https://www.ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/R6R3UQ8V)

Automatically detect and set an app locale that matches your visitor's preference.

- Define your supported locales and match your visitor's preference
- Uses the most common locale [detectors](#detectors) by default
- Uses the most common locale [stores](#stores) by default
- Easily create and add your own detectors and stores

## Requirements

- PHP >= 7.1
- Laravel >= 5.6

## Install

Install this package with Composer:

```bash
composer require codezero/laravel-localizer
```

Laravel will automatically register the ServiceProvider.

## Add Middleware

Add the middleware to the `web` middleware group in `app/Http/Kernel.php`.
Make sure to add it after `StartSession` and before `SubstituteBindings`:

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

In Laravel 6.x and higher, you also need to add the middleware to the `$middlewarePriority` array in `app/Http/Kernel.php`
to trigger it in the correct order:

```php
protected $middlewarePriority = [
    \Illuminate\Session\Middleware\StartSession::class, // <= after this
    //...
    \CodeZero\Localizer\Middleware\SetLocale::class,
    //...
    \Illuminate\Routing\Middleware\SubstituteBindings::class, // <= before this
];
```

If you don't see the `$middlewarePriority` array in your kernel file,
then you can copy it over from the parent class `Illuminate\Foundation\Http\Kernel`.

## Configure

### Publish Configuration File

```bash
php artisan vendor:publish --provider="CodeZero\Localizer\LocalizerServiceProvider" --tag="config"
```

You will now find a `localizer.php` file in the `config` folder.

### Configure Supported Locales

Add any locales you wish to support to your published `config/localizer.php` file:

```php
'supported-locales' => ['en', 'nl', 'fr'];
```

### Configure Detectors

By default, the middleware will use the following detectors to check for a supported locale in:

1. The URL slug
2. A main omitted locale
3. The authenticated user model
4. The session
5. A cookie
6. The browser
7. The app's default locale

If you set an omitted locale, no additional detectors will run after the `OmittedLocaleDetector`.
This makes sense, because the locale will always be determined by the URL in this scenario.

You can configure the session key, cookie name and the attribute on the user model that holds the locale.
By default this is all set to `locale`. If the user model does not have this attribute, it will skip this check.

You can also choose which detectors to run and in what order.

> You can create your own detector by implementing the `\CodeZero\Localizer\Detectors\Detector` interface
> and add a reference to it in the config file. The detectors are resolved from Laravel's IOC container,
> so you can add any dependencies to your constructor.

### Configure Stores

The first supported locale that is returned by a detector will automatically be stored in:

- The session
- A cookie
- The app locale

In the configuration file, you can choose which stores to use.

> You can create your own store by implementing the `\CodeZero\Localizer\Stores\Store` interface 
> and add a reference to it in the config file. The stores are resolved from Laravel's IOC container, 
> so you can add any dependencies to your constructor.

## Testing

```bash
composer test
```

## Security

If you discover any security related issues, please [e-mail me](mailto:ivan@codezero.be) instead of using the issue tracker.

## Changelog

A complete list of all notable changes to this package can be found on the
[releases page](https://github.com/codezero-be/laravel-localizer/releases).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
