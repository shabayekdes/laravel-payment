<?php

namespace Shabayek\Payment\Tests\Unit\Paymob;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Shabayek\Payment\Facade\Payment;
use Shabayek\Payment\Models\PaymentCredential;
use Shabayek\Payment\Tests\TestCase;

/**
 * Class PaymobHandleErrorTest.
 *
 * @test
 */
class PaymobHandleErrorTest extends TestCase
{
    /** @test */
    public function test_error_when_forget_set_paymob_credentials()
    {
        PaymentCredential::where('key', 'api_key')->update(['value' => null]);
        $method_id = 2;
        $payment = Payment::via($method_id);
        $errors = $payment->getErrors();

        $this->assertIsArray($errors);
        $this->assertContains('Payment credentials (api_key) are invalid.', $errors);
    }

    /** @test*/
    public function test_get_order_data_failed()
    {
        $order_id = rand(1, 100);
        Http::fake([
            // Stub a JSON response for paymob endpoints...
            "https://accept.paymobsolutions.com/api/acceptance/transactions/{$order_id}" => Http::response(['detail' => 'incorrect credentials'], 200),
        ]);

        $method_id = 2;
        $payment = Payment::via($method_id);

        $this->callMethod($payment, 'getOrderData', [$order_id]);
        $errors = $payment->getErrors();

        $this->assertIsArray($errors);
        $this->assertContains('incorrect credentials', $errors);
    }

    /** @test*/
    public function test_payment_keys_without_address()
    {
        Http::fake([
            // Stub a JSON response for paymob endpoints...
            'https://accept.paymobsolutions.com/api/*' => Http::response([], 500),
        ]);

        $method_id = 2;
        $payment = Payment::via($method_id);

        $payment->customer(fakeCustomer());

        $token = Str::random(512);
        $order_id = rand(1, 100);
        $this->callMethod($payment, 'paymentKeyRequest', [$token, $order_id]);
        $errors = $payment->getErrors();

        $this->assertIsArray($errors);
        $this->assertContains('Payment key request not created success in paymob', $errors);
    }
}
