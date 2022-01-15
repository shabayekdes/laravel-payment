<?php

namespace Shabayek\Payment\Tests\Unit\Paymob;

use Illuminate\Support\Facades\Http;
use Shabayek\Payment\Facade\Payment;
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
        $method_id = 2;
        $payment = Payment::store($method_id);
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
        $payment = Payment::store($method_id);

        $this->callMethod($payment, 'getOrderData', [$order_id]);
        $errors = $payment->getErrors();

        $this->assertIsArray($errors);
        $this->assertContains('incorrect credentials', $errors);
    }
}
