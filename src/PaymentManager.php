<?php

namespace Shabayek\Payment;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;
use Shabayek\Payment\Drivers\CodMethod;
use Shabayek\Payment\Drivers\MastercardMethod;
use Shabayek\Payment\Drivers\PaymobMethod;
use Shabayek\Payment\Models\PaymentMethod;

/**
 * PaymentManager class.
 */
class PaymentManager
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
     * @param  int  $id
     */
    public function via(int $id)
    {
        return $this->providers[$id] ?? $this->get($id);
    }

    /**
     * Get the payment connection configuration.
     *
     * @param  $name
     * @return string
     */
    protected function get($id)
    {
        try {
            $this->gateway = $this->getMethod($id);

            return $this->providers[$id] = $this->resolve($this->gateway);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Resolve the given store.
     *
     * @param  array  $gateway
     * @return mixed
     */
    protected function resolve($gateway)
    {
        $provider = $this->getProvider();

        if (is_null($provider)) {
            throw new InvalidArgumentException("Payment gateway with [{$gateway['name']}] is not defined.");
        }

        $providerMethod = 'create'.ucfirst($provider).'Provider';

        if (method_exists($this, $providerMethod)) {
            return $this->{$providerMethod}($gateway);
        } else {
            throw new InvalidArgumentException("Gateway [{$provider}] is not supported.");
        }
    }

    /**
     * Create cod method instance.
     *
     * @param  array  $config
     * @return CodMethod
     */
    private function createCodProvider($config)
    {
        return new CodMethod($config);
    }

    /**
     * Create paymob method instance.
     *
     * @param  array  $config
     * @return PaymobMethod
     */
    private function createPaymobProvider($config)
    {
        return new PaymobMethod($config);
    }

    /**
     * Create mastercard method instance.
     *
     * @param  array  $config
     * @return MastercardMethod
     */
    private function createMastercardProvider(array $config)
    {
        return new MastercardMethod($config);
    }

    /**
     * Get the payment connection configuration.
     *
     * @param  $name
     * @return string
     */
    protected function getProvider()
    {
        return $this->gateway['provider'] ?? null;
    }

    /**
     * Get the payment method from database.
     *
     * @param [type] $id
     * @return array
     */
    private function getMethod($id)
    {
        $method = PaymentMethod::with('credentials')->find($id);

        if (! $method) {
            throw new InvalidArgumentException("Payment method [{$id}] is not found.");
        }

        return $method->toArray();
    }
}
