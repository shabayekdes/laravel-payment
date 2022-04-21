<?php

namespace Shabayek\Payment\Drivers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Shabayek\Payment\Contracts\PaymentMethodContract;

/**
 * UpgMethod class.
 *
 * @author Esmail Shabayek <esmail.shabayek@gmail.com>
 */
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
        $responseData = Arr::except(request()->all(), [
            'payment_method',
            'transaction_id',
            'token',
            'SecureHash',
            'NetworkReference',
            'PayerAccount',
            'PayerName',
            'ProviderSchemeName',
            'SystemReference',
            'success',
            'data',
        ]);

        $responseData['MerchantId'] = $this->mID;
        $responseData['TerminalId'] = $this->tID;
        ksort($responseData);
        $string = [];
        foreach ($responseData as $key => $value) {
            $string[] = "{$key}={$value}";
        }
        $string = implode('&', $string);

        $generatedHash = hash_hmac('sha256', $string, hex2bin($this->secureKey));
        $generatedHash = strtoupper($generatedHash);

        $isSuccess = $generatedHash == $request->get('SecureHash') && $request->get('success') == 0;

        $data = [
            'payment_reference'  => $request->get('SystemReference'),
            'payment_transaction_id' => $responseData['transaction_id'],
        ];

        return [
            'success' => $isSuccess,
            'message' => $isSuccess ? 'Payment completed successfully' : 'Transaction did not completed', ,
            'data'    => $isSuccess ? $data : [],
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
        throw new Exception('Not implemented yet.');
    }
}
