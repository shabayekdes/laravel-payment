<?php

namespace Shabayek\Payment\Tests\Unit;

use Shabayek\Payment\Facade\Payment;
use Shabayek\Payment\PaymentManager;
use Shabayek\Payment\Tests\TestCase;
use Shabayek\Payment\Drivers\CodMethod;

/**
 * Class PaymentManagerTest
 * @test
 */
class PaymentManagerTest extends TestCase
{
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
    public function a_payment_facade_invalid_exception_if_method_not_found(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $method_id = 0;
        $payment = Payment::store($method_id);
    }
}
