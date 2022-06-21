<?php

namespace Shabayek\Payment;

use Shabayek\Payment\Drivers\CodMethod;
use Shabayek\Payment\Drivers\MastercardMethod;
use Shabayek\Payment\Drivers\PaymobMethod;
use Shabayek\Payment\Drivers\PaytabsMethod;
use Shabayek\Payment\Drivers\UpgMethod;
use Shabayek\Payment\Exceptions\NotFoundGatewayException;
use Shabayek\Payment\Models\PaymentMethod;

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
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

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
     * @return mixed|string
     *
     * @throws \Shabayek\Payment\Exceptions\NotFoundGatewayException
     */
    public function via(int $id)
    {
        return $this->providers[$id] ?? $this->get($id);
    }

    /**
     * Get the payment connection configuration.
     *
     * @param  int  $id
     * @return string
     *
     * @throws \Shabayek\Payment\Exceptions\NotFoundGatewayException
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

        if (isset($this->customCreators[$provider])) {
            return $this->callCustomCreator($provider, $gateway);
        }

        $providerMethod = 'create'.ucfirst($provider).'Provider';

        if (method_exists($this, $providerMethod)) {
            return $this->{$providerMethod}($gateway);
        }
        throw new NotFoundGatewayException("Gateway [{$provider}] is not supported.");
    }

    /**
     * Call a custom driver creator.
     *
     * @param  string  $driver
     * @param $gateway
     * @return mixed
     */
    protected function callCustomCreator(string $driver, $gateway)
    {
        return new $this->customCreators[$driver]($gateway);
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string  $driver
     * @param  string  $provider
     * @return $this
     */
    public function extend(string $driver, string $provider): self
    {
        $this->customCreators[$driver] = $provider;

        return $this;
    }

    /**
     * Create cod method instance.
     *
     * @param  array  $config
     * @return \Shabayek\Payment\Drivers\CodMethod
     */
    private function createCodProvider(array $config): CodMethod
    {
        return new CodMethod($config);
    }

    /**
     * Create paymob method instance.
     *
     * @param  array  $config
     * @return \Shabayek\Payment\Drivers\PaymobMethod
     */
    private function createPaymobProvider(array $config): PaymobMethod
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
     * @param  array  $config
     * @return \Shabayek\Payment\Drivers\PaytabsMethod
     */
    public function createPaytabsProvider(array $config): PaytabsMethod
    {
        return new PaytabsMethod($config);
    }

    /**
     * Create paytabs method instance.
     *
     * @param  array  $config
     * @return \Shabayek\Payment\Drivers\UpgMethod
     */
    public function createUpgProvider(array $config): UpgMethod
    {
        return new UpgMethod($config);
    }

    /**
     * Get the payment connection configuration.
     *
     * @return string
     */
    protected function getProvider()
    {
        return $this->gateway['provider'] ?? null;
    }

    /**
     * Get the payment method from database.
     *
     * @param  int  $id
     * @return array
     *
     * @throws \Shabayek\Payment\Exceptions\NotFoundGatewayException
     */
    private function getMethod($id): array
    {
        $method = PaymentMethod::with('credentials')->find($id);

        if (! $method) {
            throw new NotFoundGatewayException("Payment method [{$id}] is not found.");
        }

        return $method->toArray();
    }
}
