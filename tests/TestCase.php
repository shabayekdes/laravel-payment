<?php

namespace Shabayek\Payment\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Shabayek\Payment\Providers\PaymentServiceProvider;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;

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
    }

    /**
     * Get package serivce providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
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
     * @param [type] $app
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
