<?php

namespace Shabayek\Payment\Tests\Unit\Paymob;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Shabayek\Payment\Facade\Payment;
use Shabayek\Payment\Tests\TestCase;

/**
 * Class PaymobMethodTest
 * @test
 */
class PaymobRequestTest extends TestCase
{
    /** @test*/
    public function test_authication_token_in_paymob_method()
    {
        Http::fake([
            // Stub a JSON response for paymob endpoints...
            'https://accept.paymobsolutions.com/api/*' => Http::response(['token' => Str::random(512)], 200),
        ]);

        $method_id = 2;
        $payment = Payment::store($method_id);

        $token = $this->callMethod($payment, 'getAuthenticationToken');

        $this->assertNotEquals(false, $token);
        $this->assertEquals(gettype($token), 'string');
    }
    /** @test*/
    public function test_create_order_without_set_customer_details_in_paymob_method()
    {
        $this->expectException(\Exception::class);

        Http::fake([
            // Stub a JSON response for paymob endpoints...
            'https://accept.paymobsolutions.com/api/*' => Http::response([], 200),
        ]);

        $method_id = 2;
        $payment = Payment::store($method_id);

        $token = Str::random(512);
        $order = $this->callMethod($payment, 'orderCreation', [$token]);
    }
}
