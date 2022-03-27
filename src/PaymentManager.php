<?php

namespace Shabayek\Payment;

use InvalidArgumentException;
use Illuminate\Support\Manager;
use Shabayek\Payment\Drivers\CodMethod;
use Shabayek\Payment\Drivers\PaymobMethod;
use Shabayek\Payment\Drivers\MastercardMethod;
use Illuminate\Contracts\Foundation\Application;

/**
 * PaymentManager class.
 */
class PaymentManager extends Manager
{
    /**
     * The application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * The array of resolved payment stores.
     *
     * @var array
     */
    protected $stores = [];

    /**
     * Create a new payment manager instance.
     *
     * @param  Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Get a payment store instance by name, wrapped in a repository.
     *
     * @param  int|null  $name
     */
    public function store(int $id = null)
    {
        $id = $id ?: $this->getDefaultDriver();

        return $this->stores[$id] = $this->get($id);
    }

    /**
     * Attempt to get the store from the local payment.
     *
     * @param $id
     * @return mixed
     */
    protected function get($id)
    {
        return $this->stores[$id] ?? $this->resolve($id);
    }

    /**
     * Resolve the given store.
     *
     * @param $id
     * @return mixed
     */
    protected function resolve($id)
    {
        $config = $this->getConfig($id);

        if (is_null($config)) {
            throw new InvalidArgumentException("Payment store [{$id}] is not defined.");
        }

        $driverMethod = 'create'.ucfirst($config['driver']).'Method';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        } else {
            throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
        }
    }

    /**
     * Create cod method instance.
     *
     * @param  array  $config
     * @return CodMethod
     */
    private function createCodMethod(array $config)
    {
        return new CodMethod($config);
    }

    /**
     * Create paymob method instance.
     *
     * @param  array  $config
     * @return PaymobMethod
     */
    private function createPaymobMethod(array $config)
    {
        return new PaymobMethod($config);
    }
    /**
     * Create mastercard method instance.
     *
     * @param  array  $config
     * @return MastercardMethod
     */
    private function createMastercardMethod(array $config)
    {
        return new MastercardMethod($config);
    }

    /**
     * Get the payment connection configuration.
     *
     * @param $id
     * @return array
     */
    protected function getConfig($id)
    {
        return $this->app['config']["payment.stores.{$id}"];
    }

    /**
     * Get the default payment driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->app['config']['payment.default'];
    }
}
