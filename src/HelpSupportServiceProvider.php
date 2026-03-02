<?php

namespace Iquesters\HelpSupport;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class HelpSupportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Log::debug('HelpSupportServiceProvider register started', [
            'provider' => static::class,
        ]);
    }

    public function boot(): void
    {
        Log::debug('HelpSupportServiceProvider boot started', [
            'provider' => static::class,
        ]);

        $this->registerPackageRoutes();
        $this->registerPackageViews();

        Log::debug('HelpSupportServiceProvider boot completed', [
            'provider' => static::class,
        ]);
    }

    protected function registerPackageRoutes(): void
    {
        $routesPath = __DIR__ . '/../routes/web.php';

        if (!is_file($routesPath)) {
            Log::debug('Help support routes file not found, skipping route registration', [
                'provider' => static::class,
                'path' => $routesPath,
            ]);
            return;
        }

        $this->loadRoutesFrom($routesPath);

        Log::debug('Help support routes registered', [
            'provider' => static::class,
            'path' => $routesPath,
        ]);
    }

    protected function registerPackageViews(): void
    {
        $viewsPath = __DIR__ . '/../resources/views';

        if (!is_dir($viewsPath)) {
            Log::debug('Help support views directory not found, skipping view registration', [
                'provider' => static::class,
                'path' => $viewsPath,
            ]);
            return;
        }

        $this->loadViewsFrom($viewsPath, 'helpsupport');

        Log::debug('Help support views registered', [
            'provider' => static::class,
            'path' => $viewsPath,
            'namespace' => 'helpsupport',
        ]);
    }
}
