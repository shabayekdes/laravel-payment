<?php

namespace Shabayek\Payment\Tests\Feature\Paymob;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Shabayek\Payment\Facade\Payment;
use Shabayek\Payment\Tests\TestCase;
use Illuminate\Support\Facades\Config;

/**
 * Class PaymobMethodFeature
 * @test
 */
class PaymobMethodFeature extends TestCase
{
    /** @test*/
    public function test_payment_token_in_return_purchase_url_with_paymob()
    {
        $order_id = rand(1, 100);
        $payment_key = Str::random(512);
        Http::fake([
            // Stub a JSON response for paymob endpoints...
            'https://accept.paymobsolutions.com/api/auth/tokens' => Http::response(['token' => Str::random(512)], 200),
            'https://accept.paymobsolutions.com/api/ecommerce/orders' => Http::response(['id' => $order_id], 200),
            'https://accept.paymobsolutions.com/api/acceptance/payment_keys' => Http::response(['token' => $payment_key], 200),
        ]);

        $method_id = 2;
        $payment = Payment::store($method_id);

        $payment->customer($this->customer());
        $payment->address($this->address());
        $payment->items($this->items());

        $payUrl = $payment->purchase();
        $query = [];
        $parts = parse_url($payUrl, PHP_URL_QUERY);
        parse_str($parts, $query);


        $this->assertEquals($query['payment_token'], $payment_key);
    }
    /** @test*/
    public function test_iframe_in_return_purchase_url_with_paymob()
    {
        $iframe = rand(1000, 9999);
        Config::set('payment.stores.2.credentials.iframe_id', $iframe);
        $order_id = rand(1, 100);
        $payment_key = Str::random(512);
        Http::fake([
            // Stub a JSON response for paymob endpoints...
            'https://accept.paymobsolutions.com/api/auth/tokens' => Http::response(['token' => Str::random(512)], 200),
            'https://accept.paymobsolutions.com/api/ecommerce/orders' => Http::response(['id' => $order_id], 200),
            'https://accept.paymobsolutions.com/api/acceptance/payment_keys' => Http::response(['token' => $payment_key], 200),
        ]);

        $method_id = 2;
        $payment = Payment::store($method_id);

        $payment->customer($this->customer());
        $payment->address($this->address());
        $payment->items($this->items());

        $payUrl = $payment->purchase();

        $parts = parse_url($payUrl, PHP_URL_PATH);
        $arr = explode('/', $parts);

        $this->assertEquals($arr[4], $iframe);
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
