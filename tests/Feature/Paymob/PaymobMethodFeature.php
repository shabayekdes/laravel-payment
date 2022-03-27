<?php

namespace Shabayek\Payment\Tests\Feature\Paymob;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Shabayek\Payment\Facade\Payment;
use Shabayek\Payment\Tests\TestCase;
use Shabayek\Payment\Models\PaymentMethod;
use Shabayek\Payment\Models\PaymentCredential;
use Shabayek\Payment\Tests\Helper\Paymob\PaymobCallback;

/**
 * Class PaymobMethodFeature.
 *
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
            'https://accept.paymobsolutions.com/api/auth/tokens'             => Http::response(['token' => Str::random(512)], 200),
            'https://accept.paymobsolutions.com/api/ecommerce/orders'        => Http::response(['id' => $order_id], 200),
            'https://accept.paymobsolutions.com/api/acceptance/payment_keys' => Http::response(['token' => $payment_key], 200),
        ]);

        $method_id = 2;
        $payment = Payment::via($method_id);

        $payment->customer(fakeCustomer());
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
        $method_id = 2;
        $iframe = rand(1000, 9999);
        PaymentCredential::where('payment_method_id', $method_id)->where('key', 'iframe_id')->update(['value' => $iframe]);

        $order_id = rand(1, 100);
        $payment_key = Str::random(512);
        Http::fake([
            // Stub a JSON response for paymob endpoints...
            'https://accept.paymobsolutions.com/api/auth/tokens'             => Http::response(['token' => Str::random(512)], 200),
            'https://accept.paymobsolutions.com/api/ecommerce/orders'        => Http::response(['id' => $order_id], 200),
            'https://accept.paymobsolutions.com/api/acceptance/payment_keys' => Http::response(['token' => $payment_key], 200),
        ]);

        $payment = Payment::via($method_id);

        $payment->customer(fakeCustomer());
        $payment->items($this->items());

        $payUrl = $payment->purchase();

        $parts = parse_url($payUrl, PHP_URL_PATH);
        $arr = explode('/', $parts);

        $this->assertEquals($arr[4], $iframe);
    }

    /** @test*/
    public function test_payment_pay_success_return_from_post_request_with_paymob()
    {
        $method_id = 2;
        PaymentCredential::where('payment_method_id', $method_id)->where('key', 'hmac_hash')->update(['value' => 'DOBJWVLKIEBRP5GZXWMHBJJV58GYLZ5R']);

        $payment = Payment::via($method_id);

        $fakeRequest = new Request();
        $fakeRequest->setMethod('POST');

        $requestData = PaymobCallback::processesCallback(24826928);
        $fakeRequest->request->add($requestData);

        $paymentCallback = $payment->pay($fakeRequest);

        $this->assertTrue($paymentCallback['success']);
        $this->assertArrayHasKey('payment_order_id', $paymentCallback['data']);
        $this->assertArrayHasKey('payment_transaction_id', $paymentCallback['data']);
    }

    /** @test*/
    public function test_payment_pay_success_return_from_get_request_with_paymob()
    {
        $method_id = 2;
        PaymentCredential::where('payment_method_id', $method_id)->where('key', 'hmac_hash')->update(['value' => 'DOBJWVLKIEBRP5GZXWMHBJJV58GYLZ5R']);
        $payment = Payment::via($method_id);

        $fakeRequest = new Request();
        $fakeRequest->setMethod('GET');

        $requestData = PaymobCallback::responseCallback('24827227', '19766521');
        $fakeRequest->replace($requestData);

        $paymentCallback = $payment->pay($fakeRequest);

        $this->assertTrue($paymentCallback['success']);
        $this->assertArrayHasKey('payment_order_id', $paymentCallback['data']);
        $this->assertArrayHasKey('payment_transaction_id', $paymentCallback['data']);
    }

    /** @test*/
    public function test_installment_payment_pay_fail_return_from_post_request_with_paymob()
    {
        $method_id = 2;
        PaymentMethod::find($method_id)->update(['is_installment' => true]);
        PaymentCredential::where('payment_method_id', $method_id)->where('key', 'hmac_hash')->update(['value' => 'DOBJWVLKIEBRP5GZXWMHBJJV58GYLZ5R']);

        Http::fake([
            // Stub a JSON response for paymob endpoints...
            'https://accept.paymobsolutions.com/api/auth/tokens' => Http::response(['token' => Str::random(512)], 200),
        ]);

        $payment = Payment::via($method_id);

        $fakeRequest = new Request();
        $fakeRequest->setMethod('POST');

        $requestData = PaymobCallback::processesCallback(24826928, false);
        $fakeRequest->request->add($requestData);

        $paymentCallback = $payment->pay($fakeRequest);

        $this->assertFalse($paymentCallback['success']);
        $this->assertEquals('Transaction did not completed', $paymentCallback['message']);
    }

    /** @test*/
    public function test_installment_payment_pay_success_return_from_post_request_with_paymob()
    {
        $method_id = 2;
        PaymentMethod::find($method_id)->update(['is_installment' => true]);
        PaymentCredential::where('payment_method_id', $method_id)->where('key', 'hmac_hash')->update(['value' => 'DOBJWVLKIEBRP5GZXWMHBJJV58GYLZ5R']);

        $requestData = PaymobCallback::processesCallback(24826928);
        Http::fake([
            // Stub a JSON response for paymob endpoints...
            'https://accept.paymobsolutions.com/api/auth/tokens' => Http::response(['token' => Str::random(512)], 200),
            // Stub a JSON response for paymob endpoints...
            "https://accept.paymobsolutions.com/api/acceptance/transactions/{$requestData['obj']['order']['id']}" => Http::response($requestData['obj'], 200),
        ]);

        $payment = Payment::via($method_id);

        $fakeRequest = new Request();
        $fakeRequest->setMethod('POST');

        $requestData = PaymobCallback::processesCallback(24826928);
        $fakeRequest->request->add($requestData);

        $paymentCallback = $payment->pay($fakeRequest);

        $this->assertTrue($paymentCallback['success']);
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

    /**
     * Get address fake data.
     *
     * @return array
     */
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
