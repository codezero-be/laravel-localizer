# Laravel Localizer

[![GitHub release](https://img.shields.io/github/release/codezero-be/laravel-localizer.svg)]()
[![License](https://img.shields.io/packagist/l/codezero/laravel-localizer.svg)]()
[![Build Status](https://scrutinizer-ci.com/g/codezero-be/laravel-localizer/badges/build.png?b=master)](https://scrutinizer-ci.com/g/codezero-be/laravel-localizer/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/codezero-be/laravel-localizer/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/codezero-be/laravel-localizer/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/codezero-be/laravel-localizer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/codezero-be/laravel-localizer/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/codezero/laravel-localizer.svg)](https://packagist.org/packages/codezero/laravel-localizer)

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

See a list of important changes in the [changelog](CHANGELOG.md).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
