<?php

namespace Shabayek\Payment\Tests\Unit\Paymob;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Shabayek\Payment\Facade\Payment;
use Shabayek\Payment\Tests\TestCase;
use Shabayek\Payment\Models\PaymentCredential;
use Shabayek\Payment\Tests\Helper\Paymob\PaymobCallback;

/**
 * Class PaymobMethodTest.
 *
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
        $payment = Payment::via($method_id);

        $token = $this->callMethod($payment, 'getAuthenticationToken');

        $this->assertNotEquals(false, $token);
        $this->assertEquals(gettype($token), 'string');
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
        $payment = Payment::via($method_id);

        $payment->customer(fakeCustomer());

        $token = Str::random(512);
        $order_id = rand(1, 100);
        $token = $this->callMethod($payment, 'paymentKeyRequest', [$token, $order_id]);

        $this->assertEquals($token, $payment_key);
    }

    /** @test*/
    public function test_paymob_processes_callback_success()
    {
        $method_id = 2;
        PaymentCredential::where('payment_method_id', $method_id)->where('key', 'hmac_hash')->update(['value' => 'DOBJWVLKIEBRP5GZXWMHBJJV58GYLZ5R']);
        $payment = Payment::via($method_id);

        $requestData = PaymobCallback::processesCallback(24826928);
        $processesCallback = $this->callMethod($payment, 'processesCallback', [$requestData]);

        $this->assertTrue($processesCallback['transaction_status']);
        $this->assertArrayHasKey('payment_order_id', $processesCallback);
        $this->assertArrayHasKey('payment_transaction_id', $processesCallback);
    }

    /** @test*/
    public function test_paymob_response_callback_success()
    {
        $method_id = 2;
        PaymentCredential::where('payment_method_id', $method_id)->where('key', 'hmac_hash')->update(['value' => 'DOBJWVLKIEBRP5GZXWMHBJJV58GYLZ5R']);
        $payment = Payment::via($method_id);

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
        $payment = Payment::via($method_id);

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

        $payment_status = Payment::via($method_id)->verify($paymob_order_id);

        $this->assertTrue($payment_status['success']);
        $this->assertEquals($paymob_order_id, $payment_status['data']['payment_order_id']);
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

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        $app['config']->set('payment.stores.2.credentials', [
            'api_key'   => 'test',
            'hmac_hash'   => 'test',
            'merchant_id'   => 'test',
            'iframe_id'   => 'test',
            'integration_id'   => 'test',
        ]);
    }
}
