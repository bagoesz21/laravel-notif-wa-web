<?php

namespace Bagoesz21\LaravelNotifWaWeb;

use Illuminate\Support\ServiceProvider;

use Bagoesz21\LaravelNotifWaWeb\Console\Commands\WhatsappSessionStatusCommand;

class LaravelNotifWaWebServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/whatsapp.php', 'whatsapp');

        // Register the service the package provides.
        $this->app->singleton('laravel-notif-wa-web', function ($app) {
            return new LaravelNotifWaWeb;
        });

        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('NotifWaWeb', 'Bagoesz21\LaravelNotifWaWeb\Facades\LaravelNotifWaWeb');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravel-notif-wa-web'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/whatsapp.php' => config_path('whatsapp.php'),
        ], 'whatsapp.config');

        // $this->publishes([
        //     __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-notif-wa'),
        // ], 'whatsapp-lang');

        // Registering package commands.
        $this->commands([
            WhatsappSessionStatusCommand::class,
        ]);
    }
}
