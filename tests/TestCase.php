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
