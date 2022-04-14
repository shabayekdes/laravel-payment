<?php

namespace Shabayek\Payment\Tests\Unit;

use Shabayek\Payment\Facade\Payment;
use Shabayek\Payment\PaymentManager;
use Shabayek\Payment\Tests\TestCase;
use Shabayek\Payment\Drivers\CodMethod;
use Shabayek\Payment\Drivers\PaymobMethod;
use Shabayek\Payment\Drivers\MastercardMethod;
use Shabayek\Payment\Exceptions\NotFoundGatewayException;

/**
 * Class PaymentManagerTest.
 *
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
        $payment = Payment::via($method_id);

        $this->assertInstanceOf(CodMethod::class, $payment);
    }

    /** @test */
    public function a_payment_facade_return_paymob_method_instance(): void
    {
        $method_id = 2;
        $payment = Payment::via($method_id);

        $this->assertInstanceOf(PaymobMethod::class, $payment);
    }

    /** @test */
    public function a_payment_facade_return_mastercard_method_instance(): void
    {
        $method_id = 3;
        $payment = Payment::via($method_id);

        $this->assertInstanceOf(MastercardMethod::class, $payment);
    }

    /** @test */
    public function a_payment_facade_invalid_exception_if_method_not_found(): void
    {
        $this->expectException(NotFoundGatewayException::class);

        $method_id = 0;
        Payment::via($method_id);
    }

    /** @test */
    public function it_can_set_amount_when_add_to_items()
    {
        $payment = Payment::via(2);
        $items = [
            'name'         => 'ASC1515',
            'amount_cents' => 500000,
            'description'  => 'Smart Watch',
            'quantity'     => '1',
        ];
        $payment->items($items);

        $reflector = new \ReflectionClass(PaymobMethod::class);
        $property = $reflector->getProperty('amount');
        $property->setAccessible(true);

        $amount = $property->getValue($payment);

        $this->assertEquals($items['amount_cents'], $amount);
    }
}
