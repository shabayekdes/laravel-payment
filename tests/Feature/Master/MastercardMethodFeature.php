<?php

namespace Shabayek\Payment\Tests\Feature\Mastercard;

use Shabayek\Payment\Enums\Gateway;
use Illuminate\Support\Facades\Http;
use Shabayek\Payment\Facade\Payment;
use Shabayek\Payment\Tests\TestCase;
use Shabayek\Payment\Models\PaymentCredential;
use Shabayek\Payment\Tests\Fixtures\Transaction;

/**
 * Class MastercardMethodFeature.
 *
 * @test
 */
class MastercardMethodFeature extends TestCase
{
    /** @test */
    public function it_can_checkout_form_mastercard_method_successfully()
    {
        $merchant_id = 'TEST_MERCHANT';
        PaymentCredential::where('payment_method_id', 3)->where('key', 'merchant_id')->update([
            'value' => $merchant_id,
        ]);
        Http::fake([
            "https://qnbalahli.test.gateway.mastercard.com/api/rest/version/61/merchant/{$merchant_id}/session" => Http::response(
                [
                    'result'           => Gateway::MASTERCARD_RESPONSE_SUCCESS,
                    'successIndicator' => 'test',
                    'session'          => [
                        'id' => '123',
                    ],
                ], 200),
        ]);

        $payment = Payment::via(3);
        $payment->items($this->items());
        $payment->customer(fakeCustomer());

        $transaction = new Transaction([
            'id' => 1000,
        ]);
        $payment->transaction($transaction->id);
        $formView = $payment->checkoutForm($transaction);

        $this->assertStringContainsString('Checkout.configure', $formView->render());
    }

    /**
     * @test
     * @expectedException  exception
     */
    public function it_can_mastercard_set_error_if_not_created_session()
    {
        $merchant_id = 'TEST_MERCHANT';
        PaymentCredential::where('payment_method_id', 3)->where('key', 'merchant_id')->update([
            'value' => $merchant_id,
        ]);

        Http::fake([
            "https://qnbalahli.test.gateway.mastercard.com/api/rest/version/61/merchant/{$merchant_id}/session" => Http::response(
                [
                    'result' => 'declined',
                ], 200),
        ]);

        $payment = Payment::via(3);
        $payment->items($this->items());
        $payment->customer(fakeCustomer());

        $transaction = new Transaction([
            'id' => 1000,
        ]);
        $payment->transaction($transaction->id);
        $payment->checkoutForm($transaction);

        $this->assertFalse($payment->isSuccess());
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
}
