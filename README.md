# deesynertz/laravel-visitor

## Features

### Installation

Using Composer run

```php
composer require deesynertz/laravel-visitor
```

### Laravel >= 5.5

That's it! The package is auto-discovered on 5.5 and up!

### Laravel <= 5.4

Add the service provider to the `providers` array in your `config/app.php` file:

```php
'providers' => [
    // Other Laravel service providers...

    /*
    * Package Service Providers...
    */
    Deesynertz\Visitor\VisitorServiceProvider::class,

    // Other package service providers...
],
```

### Usage

add HasVistors

## Contributions

Contributions and feedback are welcome! Feel free to open an issue or submit a pull request on GitHub.

## License

This package is open-source software licensed under the [MIT](https://github.com/deesynertz/laravel-visitor/blob/master/LICENSE) license.
