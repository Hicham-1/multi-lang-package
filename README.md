# H1ch4m/MultiLang 🌐

[![Latest Version](https://img.shields.io/packagist/v/h1ch4m/multi-lang.svg?style=flat-square)](https://packagist.org/packages/h1ch4m/multi-lang)
[![Total Downloads](https://img.shields.io/packagist/dt/h1ch4m/multi-lang.svg?style=flat-square)](https://packagist.org/packages/h1ch4m/multi-lang)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

A multi language solution for Laravel applications with database-driven translations, automatic language detection.

# Table of Contents

- [H1ch4m/MultiLang 🌐](#h1ch4mmultilang-)
- [Table of Contents](#table-of-contents)
- [✨ Features](#-features)
- [📋 Requirements](#-requirements)
- [⚙️ Installation](#️-installation)
  - [Config files](#config-files)
  - [Migrations](#migrations)
  - [Views](#views)
  - [Routes](#routes)
- [⚙️ Configuration](#️-configuration)
  - [Config Files](#config-files-1)
- [🔧 Advanced Usage](#-advanced-usage)
  - [Custom route](#custom-route)
    - [if you published the routes](#if-you-published-the-routes)
    - [if you want to use package routes](#if-you-want-to-use-package-routes)
- [🧰 Helpers](#-helpers)
  - [Available helper functions](#available-helper-functions)
- [🗺️ Navigation](#️-navigation)
- [🚧 Middleware](#-middleware)
- [📄 Service Provider](#-service-provider)
- [📖 Model Setup Example](#-model-setup-example)
- [🎓 License](#-license)

# ✨ Features

- 🗃️ Database-driven language management
- 🌍 Automatic language detection middleware
- ⚙️ Customizable configuration files
- 🔄 Language switcher UI component
- 📦 Built-in migration system
- 📁 Asset publishing support
- 📚 View customization
- 🛠️ Helper functions
- 🔌 Event system integration
- 🧩 Modular architecture

# 📋 Requirements

- PHP 8.0+
- Laravel 9.x or later
- spatie/laravel-translatable 6.6 or later
- Composer
- Database (MySQL)

# ⚙️ Installation

1. Install via Composer:
```bash
composer require h1ch4m/multi-lang
```

2. Publish required components:

## Config files
```bash
php artisan vendor:publish --tag=config --provider="H1ch4m\MultiLang\MultiLangServiceProvider"
```

## Migrations
```bash
php artisan vendor:publish --tag=migration --provider="H1ch4m\MultiLang\MultiLangServiceProvider"
```

<!-- # Assets (CSS/JS)
php artisan vendor:publish --tag=public --provider="H1ch4m\MultiLang\MultiLangServiceProvider" -->

## Views
```bash
php artisan vendor:publish --tag=views --provider="H1ch4m\MultiLang\MultiLangServiceProvider"
```

## Routes
```bash
php artisan vendor:publish --tag=routes --provider="H1ch4m\MultiLang\MultiLangServiceProvider"
```

3. Run migrations:

```bash
php artisan migrate
```

# ⚙️ Configuration

## Config Files

Configure these published files in ```config/``` directory:

1. ```h1ch4m_languages.php``` - Manage available languages

```php
return [
    "fr" => ["name" => "French", "is_active" => true, "is_default" => false, "flag_url" => 'https://cdn-icons-png.flaticon.com/128/323/197560.png'],
    "ar" => ["name" => "Arabic", "is_active" => true, "is_default" => false, "flag_url" => 'https://cdn-icons-png.flaticon.com/128/197/197467.png'],
    "en" => ["name" => "English", "is_active" => true, "is_default" => true, "flag_url" => 'https://cdn-icons-png.flaticon.com/128/197/197374.png'],
];
```

2. ```h1ch4m_config.php``` - Configure package layouts and paths

```php
return [
    'layout' => '', //'admin.layouts.app'
    'models_path' => app_path('Models'),
    'custom_route' => '', // panel.
    'custom_content' => 'content', // content
    'custom_javascript' => 'javascript', // javascript
    'custom_style' => 'style', // style
];
```

3. ```app.php``` - add locale and fallback language

```php
return [
    // your other configs

    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en')
]

```

# 🔧 Advanced Usage
## Custom route
### if you published the routes
```php
Route::group(['prefix' => 'panel', 'as' => 'panel.'], function () {
    require base_path('routes/vendor/h1ch4m_multi_lang.php');
});
```
### if you want to use package routes
```php
Route::group(['prefix' => 'panel', 'as' => 'panel.'], function () {
    require base_path('vendor/h1ch4m/multi-lang/src/routes/web.php');
});
```

# 🧰 Helpers
## Available helper functions
```php
// Get active languages
getActiveLanguages(): array

// Get default language
getDefaultLanguage(): string

// Get or Set location (see 'Service Provider' section)
getOrSetCachedLocale($localeLang = null): string
```
# 🗺️ Navigation

```html
    <li class="pc-item">
        <a href="{{ route('panel.languages.models') }}" class="pc-link">
            <span class="pc-micon">
                <i data-feather="globe"></i>
            </span>
            <span class="pc-mtext"> {{ __('Translation') }} </span>
        </a>
    </li>

    <li class="pc-item">
        <a href="{{ route('panel.languages.setting') }}" class="pc-link">
            <span class="pc-micon">
                <i data-feather="globe"></i>
            </span>
            <span class="pc-mtext"> {{ __('ML Setting') }} </span>
        </a>
    </li>
```
# 🚧 Middleware

in your frontend (Store, Blog...) use this middleware ```'h1ch4m_middleware'```
```php
Route::middleware(['h1ch4m_middleware'])->group(function () {
    Route::get('/your-path', [YourBlogController::class, 'method']);
});
```

# 📄 Service Provider

 in your service provider add it if you are using ```spatie/laravel-translatable```, you will not need to call getTranslation or setTranslation (the language will add automatically)

```php
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(): void
    {
        if (Request::is('panel/*')) {
            Config::set('app.locale', getOrSetCachedLocale());
        }
    }
}
```

# 📖 Model Setup Example

your Model should be look like this

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Program extends Model
{
    use HasTranslations;

    public $translatable = ['title', 'body', 'features'];
    public $input_type = ['text', 'editor', 'array'];
    public $custom_name = 'Program';
    public $default_title = 'title';

    // if the Model has parent (just to group programs by events)
    public $parent_method = 'event';


    protected $table = 'programs';

    protected $fillable = [
        'event_id',
        'title',
        'body',
        'features',
        'is_active',
        'is_pinned'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
```


# 🎓 License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).
