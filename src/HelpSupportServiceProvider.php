<?php

namespace Iquesters\HelpSupport;

use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider;
use Iquesters\Foundation\Enums\Module;
use Iquesters\Foundation\Support\ConfProvider;
use Iquesters\Foundation\System\Traits\Loggable;
use Iquesters\HelpSupport\Config\HelpSupportConf;
use Iquesters\HelpSupport\Database\Seeders\HelpSupportSeeder;
use Iquesters\UserInterface\Config\UserInterfaceConf;

class HelpSupportServiceProvider extends ServiceProvider
{
    use Loggable;

    public function register(): void
    {
        ConfProvider::register(Module::HELP_SUPPORT, HelpSupportConf::class);

        $this->registerSeedCommand();
    }

    public function boot(): void
    {
        $this->registerPackageRoutes();
        $this->registerPackageViews();
        $this->app->instance('app.layout', $this->getAppLayout());

        if ($this->app->runningInConsole()) {
            $this->commands([
                'command.help-support.seed',
            ]);
        }

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/help-support'),
        ], 'help-support-config');
    }

    protected function registerPackageRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }

    protected function registerPackageViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'help-support');
    }

    protected function getAppLayout(): string
    {
        if (class_exists(UserInterfaceConf::class)) {
            try {
                $uiConf = ConfProvider::from(Module::USER_INFE);

                if (method_exists($uiConf, 'ensureLoaded')) {
                    $uiConf->ensureLoaded();
                }

                return $uiConf->app_layout;
            } catch (\Throwable $e) {
                $this->logWarning('HelpSupport: failed to load UserInterface app layout: ' . $e->getMessage());
            }
        }

        return 'userinterface::layouts.app';
    }

    protected function registerSeedCommand(): void
    {
        $this->app->singleton('command.help-support.seed', function ($app) {
            return new class extends Command {
                protected $signature = 'help-support:seed';
                protected $description = 'Seed Help Support module data';

                public function handle(): int
                {
                    $this->info('Running Help Support Seeder...');

                    $seeder = new HelpSupportSeeder();
                    $seeder->setCommand($this);
                    $seeder->run();

                    $this->info('Help Support seeding completed!');

                    return self::SUCCESS;
                }
            };
        });
    }
}
