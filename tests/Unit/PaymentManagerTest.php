<?php

namespace Shabayek\Payment\Tests\Unit;

use Shabayek\Payment\Facade\Payment;
use Shabayek\Payment\PaymentManager;
use Shabayek\Payment\Tests\TestCase;
use Shabayek\Payment\Drivers\CodMethod;
use Shabayek\Payment\Drivers\PaymobMethod;

/**
 * Class PaymentManagerTest
 * @test
 */
class PaymentManagerTest extends TestCase
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
        config()->set('payment.stores.2.credentials.api_key', 'test');
        config()->set('payment.stores.2.credentials.hmac_hash', 'test');
        config()->set('payment.stores.2.credentials.merchant_id', 'test');
        config()->set('payment.stores.2.credentials.iframe_id', 'test');
        config()->set('payment.stores.2.credentials.integration_id', 'test');
    }
    /** @test */
    public function a_payment_facade_is_register(): void
    {
        $payment = app()->make('payment');
        $this->assertInstanceOf(PaymentManager::class, $payment);
    }
    /** @test */
    public function a_payment_facade_return_cod_method_instance(): void
    {
        $method_id = 1;
        $payment = Payment::store($method_id);

        $this->assertInstanceOf(CodMethod::class, $payment);
    }
    /** @test */
    public function a_payment_facade_return_paymob_method_instance(): void
    {
        $method_id = 2;
        $payment = Payment::store($method_id);

        $this->assertInstanceOf(PaymobMethod::class, $payment);
    }
    /** @test */
    public function a_payment_facade_invalid_exception_if_method_not_found(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $method_id = 0;
        $payment = Payment::store($method_id);
    }
}
