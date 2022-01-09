<?php

namespace Shabayek\Payment\Tests\Unit\Paymob;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Shabayek\Payment\Facade\Payment;
use Shabayek\Payment\Tests\Helper\Paymob\PaymobCallback;
use Shabayek\Payment\Tests\TestCase;

/**
 * Class PaymobMethodTest.
 *
 * @test
 */
class PaymobRequestTest extends TestCase
{
    /**
     * Setup test cases.
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
        $this->expectExceptionMessage('Items not set.');

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
        $this->expectExceptionMessage('Address details not set.');

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

    /** @test*/
    public function test_paymob_processes_callback_success()
    {
        config()->set('payment.stores.2.credentials.hmac_hash', 'DOBJWVLKIEBRP5GZXWMHBJJV58GYLZ5R');
        $method_id = 2;
        $payment = Payment::store($method_id);

        $requestData = PaymobCallback::processesCallback(24826928);
        $processesCallback = $this->callMethod($payment, 'processesCallback', [$requestData]);

        $this->assertTrue($processesCallback['transaction_status']);
        $this->assertArrayHasKey('payment_order_id', $processesCallback);
        $this->assertArrayHasKey('payment_transaction_id', $processesCallback);
    }

    /** @test*/
    public function test_paymob_response_callback_success()
    {
        config()->set('payment.stores.2.credentials.hmac_hash', 'DOBJWVLKIEBRP5GZXWMHBJJV58GYLZ5R');
        $method_id = 2;
        $payment = Payment::store($method_id);

        $requestData = PaymobCallback::responseCallback('24827227', '19766521');
        $processesCallback = $this->callMethod($payment, 'responseCallBack', [$requestData]);

        $this->assertTrue($processesCallback['transaction_status']);
        $this->assertArrayHasKey('payment_order_id', $processesCallback);
        $this->assertArrayHasKey('payment_transaction_id', $processesCallback);
    }

    /** @test*/
    public function test_paymob_get_order_data_api_success()
    {
        $requestData = PaymobCallback::processesCallback(24826928);
        Http::fake([
            // Stub a JSON response for paymob endpoints...
            'https://accept.paymobsolutions.com/api/auth/tokens' => Http::response(['token' => Str::random(512)], 200),
            // Stub a JSON response for paymob endpoints...
            'https://accept.paymobsolutions.com/api/acceptance/transactions/1' => Http::response($requestData['obj'], 200),
        ]);

        $method_id = 2;
        $payment = Payment::store($method_id);

        $order = $this->callMethod($payment, 'getOrderData', [1]);

        $this->assertEquals($order, $requestData['obj']);
    }

    /** @test */
    public function test_verify_payment_status_is_successfully_from_paymob_gateway()
    {
        $requestData = PaymobCallback::processesCallback(24826928);

        Http::fake([
            // Stub a JSON response for paymob endpoints...
            'https://accept.paymobsolutions.com/api/auth/tokens' => Http::response(['token' => Str::random(512)], 200),
            // Stub a JSON response for paymob endpoints...
            'ecommerce/orders/transaction_inquiry' => Http::response($requestData['obj'], 200),
        ]);

        $method_id = 2;
        $paymob_order_id = 24826928;

        $payment_status = Payment::store($method_id)->verify($paymob_order_id);

        $this->assertTrue($payment_status['success']);
        $this->assertEquals($paymob_order_id, $payment_status['data']['payment_order_id']);
    }

    /**
     * Get customer fake data.
     *
     * @return array
     */
    private function customer(): array
    {
        return [
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'phone'      => '+989120000000',
            'email'      => 'customer@test.com',
        ];
    }

    /**
     * Get items fake data.
     *
     * @return array
     */
    private function items(): array
    {
        return [
            'name'         => 'Product name',
            'description'  => 'Product description',
            'price' => 150,
            'quantity'     => 1,
        ];
    }

    private function address(): array
    {
        return [
            'floor'     => '1',
            'street'    => 'Test street',
            'city'      => 'Test city',
            'state'     => 'Test state',
            'apartment' => 'Test apartment',
            'building'  => 'Test building',
        ];
    }
}
