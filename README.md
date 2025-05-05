# Laravel Papers

[![Latest Version on Packagist](https://img.shields.io/packagist/v/schrojf/laravel-papers.svg?style=flat-square)](https://packagist.org/packages/schrojf/laravel-papers)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/schrojf/laravel-papers/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/schrojf/laravel-papers/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/schrojf/laravel-papers/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/schrojf/laravel-papers/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/schrojf/laravel-papers.svg?style=flat-square)](https://packagist.org/packages/schrojf/laravel-papers)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require schrojf/laravel-papers
```

You can publish and run the migrations with:

```bash
php artisan schrojf:publish --tag="papers-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan schrojf:publish --tag="papers-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan schrojf:publish --tag="papers-views"
```

## Usage

```php
$variable = new Schrojf\Papers();
echo $variable->echoPhrase('Hello, Viliam!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Viliam Schrojf](https://github.com/schrojf)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
