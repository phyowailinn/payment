<?php

namespace Yomafleet\Payment;
use Illuminate\Support\ServiceProvider;

class PaymentGatewayServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $source = realpath($raw = __DIR__.'/../config/payment.php') ?: $raw;

        $this->publishes([$source => config_path('payment.php')]);

        $this->mergeConfigFrom($source, 'payment');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
