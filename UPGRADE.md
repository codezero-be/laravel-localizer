# Upgrade Guide

## Upgrading To 3.0 From 2.x

### ➡ Minimum Requirements Updated

Due to PHP and PHPUnit version constraints with Laravel 11, we dropped support for Laravel 7.x, 8.x and 9.x.

- The minimum PHP version required is now 8.1
- The minimum Laravel version required is now 10.0

---

### ➡ Re-register Middleware

Laravel 11 no longer has a `app/Http/Kernel.php` to register middleware.
This is now handled in `bootstrap/app.php`.

🔸 **Actions Required**

If you use Laravel 11, register the middleware in `bootstrap/app.php` as described in the README.

## Upgrading To 2.0 From 1.x

### ➡ Minimum Requirements Updated

We dropped support for Laravel 5.6, 5.7, 5.8 and 6.x.

- The minimum PHP version required is now 7.2.5
- The minimum Laravel version required is now 7.0

---

### ➡ Names of Config Options Updated

Every config option that contained a `-` (dash) in its name has been updated and the dash is replaced by an `_` (underscore).
This is done mainly for consistency across other packages.

🔸 **Actions Required**

- Review and update your published config file accordingly.
