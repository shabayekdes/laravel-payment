<?php

namespace Shabayek\Payment\Tests;

use Orchestra\Testbench\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Shabayek\Payment\Providers\PaymentServiceProvider;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;

/**
 * TestCase class
 * @author Esmail Shabayek
 * @package Shabayek\Payment\Tests
 */
class TestCase extends BaseTestCase
{
    // protected $loadEnvironmentVariables = true;

    /**
     * Setup test cases
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
     * @param [type] $app
     * @return void
     */
    protected function getPackageProviders($app)
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
     * Change accessible for any method in class
     *
     * @param object $obj
     * @param string $name
     * @param array $args
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
