<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Console\Commands\InstallCommand;
use App\LighthouseDashboard;
use App\Listeners\ManipulateResultListener;
use Illuminate\Support\Facades\Event;
use Nuwave\Lighthouse\Events\ManipulateResult;

class LighthouseDashboardServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->setupInertia();

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'lighthouse-dashboard');
        $this->loadFactoriesFrom(__DIR__ . '/../../database/factories');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/lighthouse-dashboard.php', 'lighthouse-dashboard');

        // Register the service the package provides.
        $this->app->singleton('LighthouseDashboard', function ($app) {
            return new LighthouseDashboard;
        });

        // Register Event listener
        Event::listen(ManipulateResult::class, ManipulateResultListener::class);
    }

    public function provides()
    {
        return ['lighthouse-dashboard'];
    }

    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../../config/lighthouse-dashboard.php' => config_path('lighthouse-dashboard.php'),
        ], 'lighthouse-dashboard');

        // Publishing assets.
        $this->publishes([
            __DIR__ . '/../../public/vendor/lighthouse-dashboard' => public_path('vendor/lighthouse-dashboard'),
        ], 'lighthouse-dashboard');

        // Registering package commands.
        $this->commands([
            InstallCommand::class
        ]);
    }

    private function setupInertia()
    {
        Inertia::version(function () {
            return md5_file(public_path('vendor/lighthouse-dashboard/mix-manifest.json'));
        });

        Inertia::setRootView('lighthouse-dashboard::app');
    }
}
