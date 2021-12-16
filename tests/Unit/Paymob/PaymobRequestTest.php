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
    public function test_authication_token()
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
    public function test_create_order_without_set_customer_details()
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
    /** @test*/
    public function test_create_order_without_items_details()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Items not set.");

        Http::fake([
            // Stub a JSON response for paymob endpoints...
            'https://accept.paymobsolutions.com/api/*' => Http::response([], 200),
        ]);

        $method_id = 2;
        $payment = Payment::store($method_id);

        $payment->customer($this->customer());

        $token = Str::random(512);
        $this->callMethod($payment, 'orderCreation', [$token]);
    }

    /** @test*/
    public function test_create_order_success()
    {
        $order_id = rand(1, 100);
        Http::fake([
            // Stub a JSON response for paymob endpoints...
            'https://accept.paymobsolutions.com/api/*' => Http::response(['id' => $order_id], 200),
        ]);

        $method_id = 2;
        $payment = Payment::store($method_id);

        $payment->customer($this->customer());
        $payment->items($this->items());

        $token = Str::random(512);
        $order = $this->callMethod($payment, 'orderCreation', [$token]);

        $this->assertTrue(isset($order['id']));
        $this->assertEquals($order_id, $order['id']);
    }
    /** @test*/
    public function test_payment_keys_without_address()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Address details not set.");

        Http::fake([
            // Stub a JSON response for paymob endpoints...
            'https://accept.paymobsolutions.com/api/*' => Http::response([], 200),
        ]);

        $method_id = 2;
        $payment = Payment::store($method_id);

        $payment->customer($this->customer());

        $token = Str::random(512);
        $order_id = rand(1, 100);
        $this->callMethod($payment, 'paymentKeyRequest', [$token, $order_id]);
    }
    /** @test*/
    public function test_payment_keys_success()
    {
        $payment_key = Str::random(512);
        Http::fake([
            // Stub a JSON response for paymob endpoints...
            'https://accept.paymobsolutions.com/api/*' => Http::response(['token' => $payment_key], 200),
        ]);

        $method_id = 2;
        $payment = Payment::store($method_id);

        $payment->customer($this->customer());
        $payment->address($this->address());

        $token = Str::random(512);
        $order_id = rand(1, 100);
        $token = $this->callMethod($payment, 'paymentKeyRequest', [$token, $order_id]);

        $this->assertEquals($token, $payment_key);
    }
    /**
     * Get customer fake data
     *
     * @return array
     */
    private function customer(): array
    {
        return [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '+989120000000',
            'email' => 'customer@test.com',
        ];
    }
    /**
     * Get items fake data
     *
     * @return array
     */
    private function items(): array
    {
        return [
            "name" => "Product name",
            "description" => "Product description",
            "amount_cents" => 15000,
            "quantity" => 1
        ];
    }

    private function address(): array
    {
        return [
            'floor' => '1',
            'street' => 'Test street',
            'city' => 'Test city',
            'state' => 'Test state',
            'apartment' => 'Test apartment',
            'building' => 'Test building'
        ];
    }
}
