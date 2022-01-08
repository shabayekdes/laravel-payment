<?php

namespace Shabayek\Payment\Tests\Unit;

use Illuminate\Http\Request;
use Shabayek\Payment\Facade\Payment;
use Shabayek\Payment\Tests\TestCase;

/**
 * Class CodMethodTest.
 *
 * @test
 */
class CodMethodTest extends TestCase
{
    /** @test*/
    public function test_cod_method_should_have_required_function()
    {
        $method_id = 1;
        $payment = Payment::store($method_id);

        $this->assertTrue(method_exists($payment, 'purchase'));
        $this->assertTrue(method_exists($payment, 'pay'));
    }

    /** @test*/
    public function test_cod_method_purchase_should_return_null_value()
    {
        $method_id = 1;
        $payment = Payment::store($method_id);

        $this->assertNull($payment->purchase());
    }

    /** @test*/
    public function test_cod_method_should_pay_function_return_array_of_data()
    {
        $method_id = 1;
        $payment = Payment::store($method_id);
        $mockRequest = new Request();
        $pay = $payment->pay($mockRequest);

        $this->assertCount(3, $pay);
        $this->assertArrayHasKey('success', $pay);
    }

    /** @test*/
    public function test_cod_method_should_pay_function_return_success()
    {
        $method_id = 1;
        $payment = Payment::store($method_id);
        $mockRequest = new Request();
        $pay = $payment->pay($mockRequest);

        $this->assertArrayHasKey('success', $pay);
        $this->assertTrue($pay['success']);
    }
}
