<?php

namespace Shabayek\Payment\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Shabayek\Payment\Providers\PaymentServiceProvider;

/**
 * TestCase class
 * @author Esmail Shabayek
 * @package Shabayek\Payment\Tests
 */
class TestCase extends BaseTestCase
{
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
      // perform environment setup
    }
}
