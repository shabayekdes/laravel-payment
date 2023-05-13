<?php

namespace Shabayek\Payment\Tests\Feature\Paytabs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Shabayek\Payment\Enums\Gateway;
use Shabayek\Payment\Facade\Payment;
use Shabayek\Payment\Tests\TestCase;

/**
 * Class PaytabsMethodFeature.
 *
 * @test
 */
class PaytabsMethodFeature extends TestCase
{
    /** @test */
    public function it_can_purchase_with_paytabs_method_successfully()
    {
        $redirectUrl = 'http://example.com/redirect_url';
        Http::fake([
            // Stub a JSON response for paytabs endpoints...
            'https://secure-egypt.paytabs.com/payment/request' => Http::response([
                'redirect_url' => $redirectUrl,
                'tran_ref' => Str::random(20),
            ], 200),
        ]);

        $payment = Payment::via(4);
        $payment->items(fakeItems());
        $payment->customer(fakeCustomer());

        $url = $payment->purchase();

        $this->assertEquals($url, $redirectUrl);
    }

    /**
     * @test
     *
     * @expectedException  exception
     */
    public function it_can_pay_with_paytabs_method_successfully()
    {
        $payment = Payment::via(4);

        $fakeRequest = new Request();

        $fakeRequest->merge([
            'payment_result' => [
                'response_status' => Gateway::PAYTABS_RESPONSE_SUCCESS,
            ],
            'respStatus' => Gateway::PAYTABS_RESPONSE_SUCCESS,
        ]);

        $response = $payment->pay($fakeRequest);

        $this->assertTrue($response['success']);
        $this->assertEquals($response['message'], 'Payment Successfully');
    }

    /** @test */
    public function it_can_verify_with_paytabs_payment_successfully()
    {
        $tranRef = Str::random(10);
        Http::fake([
            // Stub a JSON response for paytabs endpoints...
            'https://secure-egypt.paytabs.com/payment/query' => Http::response([
                'tran_ref' => $tranRef,
                'profile_id' => 111,
                'payment_result' => [
                    'response_status' => Gateway::PAYTABS_RESPONSE_SUCCESS,
                ],
            ], 200),
        ]);

        $payment = Payment::via(4);

        $payment_order_id = 123;
        $response = $payment->verify($payment_order_id);

        $this->assertTrue($response['success']);
        $this->assertEquals($response['message'], 'Verify payment status successfully');
    }
}
