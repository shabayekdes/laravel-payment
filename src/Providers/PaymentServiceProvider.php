<?php

namespace Shabayek\Payment\Providers;

use Shabayek\Payment\PaymentManager;
use Illuminate\Support\ServiceProvider;

/**
 * PaymentServiceProvider class
 * @package Shabayek\Payment\Providers
 */
class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/payment.php', 'payment');

        $this->app->bind('payment', function () {
            return new PaymentManager($this->app);
        });
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/payment.php' => config_path('payment.php'),
        ]);
    }
}
