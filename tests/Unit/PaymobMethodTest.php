<?php

namespace Shabayek\Payment\Tests\Unit;

use Shabayek\Payment\Facade\Payment;
use Shabayek\Payment\Tests\TestCase;

/**
 * Class PaymobMethodTest
 * @test
 */
class PaymobMethodTest extends TestCase
{
    /** @test*/
    public function test_paymob_method_should_have_required_function()
    {
        $method_id = 2;
        $payment = Payment::store($method_id);

        $token = $this->callMethod($payment, 'getAuthenticationToken');

        $this->assertNotEquals(false, $token);
        $this->assertEquals(gettype($token), 'string');
    }
}
