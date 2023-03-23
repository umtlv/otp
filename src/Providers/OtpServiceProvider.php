<?php

namespace Axel\Otp\Providers;

use Axel\Otp\Console\InstallCommand;
use Axel\Otp\Console\MigrationsCommand;
use Illuminate\Support\ServiceProvider;

class OtpServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class
            ]);

            $this->publishes([__DIR__ . '/../../config/otp.php' => config_path('otp.php')], 'otp-config');

            $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        }
    }
}