<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Resolve NativePHP or default SQLite database file path
        $dbPath = config('database.connections.nativephp.database') 
                  ?? config('database.connections.sqlite.database');

        // 2. Auto-create directory and database file if missing
        if ($dbPath && $dbPath !== ':memory:' && !file_exists($dbPath)) {
            File::ensureDirectoryExists(dirname($dbPath));
            File::put($dbPath, '');
        }
        // 3. Automatically run database migrations on desktop launch
        // 4. Register custom DemoUrlGenerator to automatically resolve route names in demo mode
        $this->app->extend('url', function ($url, $app) {
            $custom = new \App\Services\DemoUrlGenerator(
                $app['router']->getRoutes(),
                $app['request']
            );
            if ($app->bound('session.store')) {
                $custom->setSessionResolver(fn () => $app['session.store']);
            }
            return $custom;
        });
    }
}

