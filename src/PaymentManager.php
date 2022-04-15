<?php

namespace Shabayek\Payment;

use Shabayek\Payment\Drivers\CodMethod;
use Shabayek\Payment\Drivers\PaymobMethod;
use Shabayek\Payment\Models\PaymentMethod;
use Shabayek\Payment\Drivers\PaytabsMethod;
use Shabayek\Payment\Drivers\MastercardMethod;
use Shabayek\Payment\Exceptions\NotFoundGatewayException;

/**
 * Payment manager class.
 *
 * @author Esmail Shabayek <esmail.shabayek@gmail.com>
 */
class PaymentManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The array of resolved payment providers.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * Create a new payment manager instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
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
        $this->gateway = $this->getMethod($id);

        return $this->providers[$id] = $this->resolve($this->gateway);
    }

    /**
     * Resolve the given gateway.
     *
     * @param  array  $gateway
     * @return mixed
     *
     * @throws \Shabayek\Payment\Exceptions\NotFoundGatewayException
     */
    protected function resolve($gateway)
    {
        $provider = $this->getProvider();

        if (is_null($provider)) {
            throw new NotFoundGatewayException("Payment gateway with [{$gateway['name']}] is not defined.");
        }

        $providerMethod = 'create'.ucfirst($provider).'Provider';

        if (method_exists($this, $providerMethod)) {
            return $this->{$providerMethod}($gateway);
        } else {
            throw new NotFoundGatewayException("Gateway [{$provider}] is not supported.");
        }
    }

    /**
     * Create cod method instance.
     *
     * @param  array  $config
     * @return \Shabayek\Payment\Drivers\CodMethod
     */
    private function createCodProvider($config): CodMethod
    {
        return new CodMethod($config);
    }

    /**
     * Create paymob method instance.
     *
     * @param  array  $config
     * @return \Shabayek\Payment\Drivers\PaymobMethod
     */
    private function createPaymobProvider($config): PaymobMethod
    {
        return new PaymobMethod($config);
    }

    /**
     * Create mastercard method instance.
     *
     * @param  array  $config
     * @return \Shabayek\Payment\Drivers\MastercardMethod
     */
    private function createMastercardProvider(array $config): MastercardMethod
    {
        return new MastercardMethod($config);
    }

    /**
     * Create paytabs method instance.
     *
     * @param array $config
     * @return \Shabayek\Payment\Drivers\PaytabsMethod
     */
    public function createPaytabsProvider(array $config): PaytabsMethod
    {
        return new PaytabsMethod($config);
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
     * @param  mixed  $id
     * @return array
     *
     * @throws \Shabayek\Payment\Exceptions\NotFoundGatewayException
     */
    private function getMethod($id)
    {
        $method = PaymentMethod::with('credentials')->find($id);

        if (! $method) {
            throw new NotFoundGatewayException("Payment method [{$id}] is not found.");
        }

        return $method->toArray();
    }
}
