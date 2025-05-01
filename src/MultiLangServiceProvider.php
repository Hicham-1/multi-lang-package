<?php

namespace H1ch4m\MultiLang;

use Illuminate\Support\ServiceProvider;
use H1ch4m\MultiLang\middleware\FrontLanguage;

class MultiLangServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Timestamp for migration file
        $timestamp = date('Y_m_d_His');
        $stubMigration = __DIR__ . '/database/migrations/2025_05_01_143564_create_multi_languages_table.php';
        $migrationDestination = database_path("migrations/{$timestamp}_create_multi_languages_table.php");

        // Allow publishing public assets
        $this->publishes([
            __DIR__ . '/resources/assets/css' => public_path('h1ch4m-multi-lang/css'),
            __DIR__ . '/resources/assets/js' => public_path('h1ch4m-multi-lang/js'),
        ], 'public');

        // Allow publishing the migration
        $this->publishes([
            $stubMigration => $migrationDestination,
        ], 'migration');

        // Allow publishing config files
        $this->publishes([
            __DIR__ . '/config/h1ch4m_multi_languages.php' => config_path('h1ch4m_multi_languages.php'),
            __DIR__ . '/config/h1ch4m_languages.php' => config_path('h1ch4m_languages.php'),
            __DIR__ . '/config/h1ch4m_config.php' => config_path('h1ch4m_config.php'),
        ], 'config');

        // Allow publishing views
        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/h1ch4m'),
        ], 'views');

        $customRoutePath = base_path('routes/vendor/h1ch4m_multi_lang.php');

        // Allow publishing route
        $this->publishes([
            __DIR__ . '/routes/web.php' => $customRoutePath,
        ], 'routes');


        if (!file_exists($customRoutePath)) {
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        }

        // Load middleware alias
        $this->app['router']->aliasMiddleware('h1ch4m_middleware', FrontLanguage::class);

        // Load views directly from package
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'h1ch4m');
    }

    public function register()
    {
        // Merge default config so package works without publishing
        $this->mergeConfigFrom(
            __DIR__ . '/config/h1ch4m_multi_languages.php',
            'h1ch4m_multi_languages'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/config/h1ch4m_languages.php',
            'h1ch4m_languages'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/config/h1ch4m_config.php',
            'h1ch4m_config'
        );

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // Autoload helper functions
        if (file_exists(__DIR__ . '/helpers.php')) {
            require_once __DIR__ . '/helpers.php';
        }
    }
}
