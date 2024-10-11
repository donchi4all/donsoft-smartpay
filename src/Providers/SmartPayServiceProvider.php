<?php

namespace Donsoft\SmartPay\Providers;

use Donsoft\SmartPay\Services\Routing\PaymentRouter;
use Illuminate\Support\ServiceProvider;

class SmartPayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        // Register the payment router as a singleton
        $this->app->singleton('payment.router', function ($app) {
            return new PaymentRouter();
        });

        // Load and merge the package configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/smartpay.php', // Correct path to your package config file
            'smartpay' // Key under which to merge the config
        );
    }

    /**
     * Boot services.
     */
    public function boot()
    {
        // Publish the configuration file
        $this->publishes([
            __DIR__ . '/../config/smartpay.php' => config_path('smartpay.php'), // Ensures correct publishing
        ], 'config'); // Tagging the publishable resource as 'config'
    }
}
