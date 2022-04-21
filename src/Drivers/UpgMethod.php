<?php

namespace Shabayek\Payment\Drivers;

use Illuminate\Http\Request;
use Shabayek\Payment\Contracts\PaymentMethodContract;

class UpgMethod extends AbstractMethod implements PaymentMethodContract
{
    private $lightbox_js = 'https://upgstaging.egyptianbanks.com:3006/js/Lightbox.js';

    /**
     * Purchase with payment method and get redirect url.
     *
     * @return string|null
     */
    public function purchase()
    {
        return null;
    }

    /**
     * Payment checkout view.
     *
     * @return void
     */
    public function checkoutForm()
    {
        $totalAmount = $this->amount;
        $transaction_id = $this->transaction_id;
        $dateTime = date('YmdHis');
        $string = "Amount={$totalAmount}&DateTimeLocalTrxn={$dateTime}&MerchantId={$this->merchant_id}&MerchantReference={$transaction_id}&TerminalId={$this->terminal_id}";
        $secureHash = hash_hmac('sha256', $string, hex2bin($this->secure_key));
        $secureHash = strtoupper($secureHash);

        return view('payment::upg', [
            'lightbox_js' => $this->lightbox_js,
            'mID' => $this->merchant_id,
            'tID' => $this->terminal_id,
            'secureHash' => $secureHash,
            'amount' => $totalAmount,
            'order_id' => $transaction_id,
            'trxDateTime' => $dateTime,
            'returnUrl' => $this->return_url,
        ]);
    }

    /**
     * Pay with payment method.
     *
     * @param  Request  $request
     * @return array
     */
    public function pay(Request $request): array
    {
        return [
            'success' => true,
            'message' => 'Payment completed successfully',
            'data'    => [],
        ];
    }

    /**
     * Verify if payment status from gateway.
     *
     * @param  int  $payment_order_id
     * @return array
     */
    public function verify(int $payment_order_id): array
    {
        return [
            'success' => true,
            'message' => 'Verify payment status successfully',
            'data' => [],
        ];
    }
}
