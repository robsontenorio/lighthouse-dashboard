<?php

namespace LighthouseDashboard\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use LighthouseDashboard\LighthouseDashboard;

class LighthouseDashboardServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Inertia::version(function () {
            return md5_file(public_path('vendor/lighthouse-dashboard/mix-manifest.json'));
        });

        Inertia::setRootView('lighthouse-dashboard::app');

        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'robsontenorio');
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

        // Event service provider
        $this->app->register(EventServiceProvider::class);
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
        ], 'lighthouse-dashboard.config');

        // Publishing assets.
        $this->publishes([
            __DIR__ . '/../../public/vendor/lighthouse-dashboard' => public_path('vendor/lighthouse-dashboard'),
        ], 'lighthouse-dashboard.assets');


        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/robsontenorio'),
        ], 'lighthouse-dashboard.views');*/


        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/robsontenorio'),
        ], 'lighthouse-dashboard.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
