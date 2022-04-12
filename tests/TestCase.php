<?php

namespace Shabayek\Payment\Tests;

use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Orchestra\Testbench\TestCase as Orchestra;
use Shabayek\Payment\Models\PaymentMethod;
use Shabayek\Payment\Providers\PaymentServiceProvider;

/**
 * TestCase class.
 *
 * @author Esmail Shabayek
 */
abstract class TestCase extends Orchestra
{
    // protected $loadEnvironmentVariables = true;

    /**
     * Setup test cases.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        // additional setup
        $this->setUpDatabase($this->app);
    }

    /**
     * Set up the database.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function setUpDatabase($app)
    {
        include_once __DIR__.'/../database/migrations/2022_03_27_210209_create_payment_methods_table.php';
        include_once __DIR__.'/../database/migrations/2022_03_27_210557_create_payment_credentials_table.php';

        (new \CreatePaymentMethodsTable())->up();
        (new \CreatePaymentCredentialsTable())->up();

        $methods = $app['config']->get('payment.stores');

        foreach ($methods as $method) {
            $credentials = $method['credentials'];
            unset($method['credentials']);

            $gateway = PaymentMethod::create($method);
            if (! empty($credentials)) {
                foreach ($credentials as $key => $value) {
                    $gateway->credentials()->create([
                        'key' => $key,
                        'value' => $value,
                    ]);
                }
            }
        }
    }

    /**
     * Get package service providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            PaymentServiceProvider::class,
        ];
    }

    /**
     * Get environment set up.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // make sure, our .env file is loaded
        $app->useEnvironmentPath(__DIR__.'/..');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);

        $app['config']->set('payment.stores.2.credentials', [
            'api_key' => env('PAYMOB_API_KEY'),
            'hmac_hash' => env('PAYMOB_HMAC_HASH'),
            'merchant_id' => env('PAYMOB_MERCHANT_ID'),
            'iframe_id' => env('PAYMOB_CARD_IFRAME_ID'),
            'integration_id' => env('PAYMOB_CARD_INTEGRATION_ID'),
        ]);

        $app['config']->set('payment.stores.3.credentials', [
            'username'     => env('QNB_USERNAME'),
            'password'     => env('QNB_PASSWORD'),
            'base_url'     => env('QNB_BASE_URL'),
            'callback_url' => env('QNB_CALLBACK_URL'),
            'checkout_js'  => env('QNB_CHECKOUT_JS'),
            'merchant_id'  => env('QNB_MERCHANT_ID'),
        ]);
        parent::getEnvironmentSetUp($app);
    }

    /**
     * Change accessible for any method in class.
     *
     * @param  object  $obj
     * @param  string  $name
     * @param  array  $args
     * @return mixed
     */
    protected function callMethod($obj, $name, array $args = [])
    {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($obj, $args);
    }
}
