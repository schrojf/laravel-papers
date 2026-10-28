# Laravel Papers

> ⚠️ **Experimental Package — Use With Caution**
>
> Laravel Papers is still in **early development** and considered experimental. While it is functional and may be used in production environments, please be aware of the following:
>
> - The API is **not stable** and may change significantly in upcoming releases.
> - Documentation may be incomplete or subject to improvement.
> - Backward compatibility between minor versions is **not guaranteed**.
> - Use at your own risk — review the source code and test thoroughly before deploying in critical systems.
>
> Feedback, issues, and contributions are very welcome as the package evolves. If you're using it in production, consider locking to a specific version.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/schrojf/laravel-papers.svg?style=flat-square)](https://packagist.org/packages/schrojf/laravel-papers)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/schrojf/laravel-papers/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/schrojf/laravel-papers/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/schrojf/laravel-papers/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/schrojf/laravel-papers/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/schrojf/laravel-papers.svg?style=flat-square)](https://packagist.org/packages/schrojf/laravel-papers)

**Laravel Papers** is a lightweight and flexible package for building and displaying custom debug or reporting pages — called *Papers* — within your Laravel application. Each Paper can aggregate and display structured or unstructured data from various sources such as databases, services, or internal logic.

Originally created as an experimental project, **Laravel Papers** is ideal for internal tools, dashboards, and developer-oriented views that are simple to register and use.

![Demo paper page with renderable components](.github/images/screenshot1.png)

## Features

- Create dynamic reporting pages using reusable components
- Render structured data using tables and data panels
- Handle forms and buttons with CSRF protected actions
- Secure access using Laravel's Gate authorization

## Requirements

- Laravel Framework 9.x, 10.x, 11.x, or 12.x
- PHP version 8.2, 8.3 or 8.4
- Modern web browser

## Installation

Install the package via Composer:

```bash
composer require schrojf/laravel-papers
```

Publish the configuration file (optional):

```bash
php artisan vendor:publish --tag="papers-config"
```

This is the default `config/papers.php` file:

```php
return [

    'middleware' => [
        'web',
        \Schrojf\Papers\Http\Middleware\AuthorizePapers::class,
    ],

    'api_middleware' => [
        'api',
        \Schrojf\Papers\Http\Middleware\AuthorizePapers::class,
    ],

];
```

Publish the views (optional):

```bash
php artisan vendor:publish --tag="papers-views"
```

## Usage

### Registering Papers

Define and register your Paper classes in a service provider, such as `AppServiceProvider.php`:

```php
use Schrojf\Papers\Papers;
use App\Papers\MyReportPaper;

public function boot(): void
{
    Papers::register([
        MyReportPaper::class,
    ]);
}
```

### Access Control

By default, access to Papers is restricted in non-local environments. Define a Gate to control who can view them:

```php
use Illuminate\Support\Facades\Gate;
use App\Models\User;

public function boot(): void
{
    Gate::define('viewPapers', function (User $user) {
        return in_array($user->email, [
            'admin@example.com',
        ]);
    });
}
```

## Creating a Paper

A Paper class extends `Schrojf\Papers\Paper` and defines its sections as callable functions. Each section returns content such as strings, arrays, or component instances like `TableContent` or `DataPanelContent`.

Here’s a full example:

```php
<?php

namespace App\Papers;

use RuntimeException;
use Schrojf\Papers\Contents\DataPanelContent;
use Schrojf\Papers\Contents\TableContent;
use Schrojf\Papers\Paper;

class DemoPaper extends Paper
{
    public static $description = 'This is a demo paper showing various content types.';
    
    public function sections(): array
    {
        return [
        
            'Simple Text' => fn () => ['Hello', 'World!'],

            'Empty' => function () {
                return null;
            },

            'With function call' => function () {
                $rand = rand(1, 100);

                return 'Random number: ' . $rand;
            },

            'Table Example' => fn () => [
                'This section demonstrates a responsive table.',
                TableContent::withHeaders(['ID', 'Name', 'Status', 'Score'])
                    ->row([1, 'Alice', 'Active', 85])
                    ->row([2, 'Bob', null, 92])
                    ->row([3, 'Charlie', 'Inactive', null])
                    ->row([4, null, 'Active', 78]),
            ],

            'Section with Data Panel Component' => function () {
                return DataPanelContent::make()
                    ->title('User Profile')
                    ->item('Name', 'Jane Doe')
                    ->item('Email', 'jane.doe@example.com')
                    ->item('Phone', '+1 234 567 8901')
                    ->item('Role', 'Administrator')
                    ->item('Status', 'Active');
            },

            'Info' => function () {
                return 'Next section will raise an exception and the Report section will not be run.';
            },

            'Exception Demo' => function () {
                throw new RuntimeException('This is a simulated error.');
            },

            'Unreachable Section' => fn () => 'This will not be rendered if the previous section fails.',
            
        ];
    }
}
```

## Actions

Sections are built for every `GET` request of the paper page, so they must never change any data. A GET request is not protected against CSRF — a link or an image on any other website opened by a signed-in user could make it.

To change data, define an optional `action()` method on the paper and submit a form from a section to the paper's URL with the `POST` method. The `POST` request runs in the `web` middleware group, so it requires a CSRF token. Papers without an `action()` method answer it with `405 Method Not Allowed`.

Dependencies of the `action()` method are resolved from the container, so you may type-hint any service or your own form request. What the method returns decides what the browser gets:

- **Nothing** — the paper page is rendered right away by the same paper instance, so sections can show what the action stored on it.
- **A response** — a redirect, a download, JSON or anything else Laravel can send is returned as it is.

```php
<?php

namespace App\Papers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Schrojf\Papers\Paper;

class ProductPrices extends Paper
{
    protected ?int $updated = null;

    public function sections(): array
    {
        $sections = [
            'Form' => fn () => new HtmlString('
                <form method="POST" action="'.e(route('papers.action', static::handler())).'">
                    '.csrf_field().'
                    <input type="number" name="discount" min="1" max="90">
                    <button type="submit" name="intent" value="discount">Apply discount</button>
                    <button type="submit" name="intent" value="export">Export prices</button>
                </form>
            '),
        ];

        if (! is_null($this->updated)) {
            $sections['Result'] = fn () => "Prices of {$this->updated} products were updated.";
        }

        return $sections;
    }

    public function action(Request $request): mixed
    {
        if ($request->input('intent') === 'export') {
            return response()->streamDownload(function () {
                echo Product::query()->pluck('price', 'id')->toJson();
            }, 'prices.json');
        }

        $validated = $request->validate([
            'discount' => ['required', 'integer', 'between:1,90'],
        ]);

        $products = Product::all();

        foreach ($products as $product) {
            $product->update([
                'price' => round($product->price * (100 - $validated['discount']) / 100, 2),
            ]);
        }

        $this->updated = $products->count();

        return null;
    }
}
```

Refreshing a page rendered after the action submits the form again, which here would apply the discount twice. Return a redirect to load the paper by a `GET` request instead, and pass its result through the session:

```php
return redirect()
    ->route('papers.show', static::handler())
    ->with('updated', $updated);
```

When the action fails validation, the browser is redirected back with the errors and the old input in the session, as anywhere else in Laravel.

Scripts in sections can call the action in the background too, e.g. for an action returning JSON. The paper page provides the token in its `csrf-token` meta tag:

```js
fetch('/papers/product-prices', {
    method: 'POST',
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    },
    body: JSON.stringify({ intent: 'email' }),
});
```

## Testing

Run the test suite using:

```bash
composer test
```

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a list of recent changes.

## Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

## Security Vulnerabilities

If you discover a security vulnerability, please review [our security policy](SECURITY.md) for how to responsibly report it.

## Credits

- [Viliam Schrojf](https://github.com/schrojf)
- [All Contributors](../../contributors)

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).
