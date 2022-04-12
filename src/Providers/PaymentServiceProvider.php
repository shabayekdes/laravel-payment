<?php

namespace Shabayek\Payment\Providers;

use Illuminate\Support\ServiceProvider;
use Shabayek\Payment\PaymentManager;

/**
 * PaymentServiceProvider class.
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

        $this->app->singleton('payment', function () {
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

        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'payment');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }
}
