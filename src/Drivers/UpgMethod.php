<?php

namespace Shabayek\Payment\Drivers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Shabayek\Payment\Contracts\CheckoutFormContract;

/**
 * UpgMethod class.
 *
 * @author Esmail Shabayek <esmail.shabayek@gmail.com>
 */
class UpgMethod extends AbstractMethod implements CheckoutFormContract
{
    protected $lightbox_js = 'https://upgstaging.egyptianbanks.com:3006/js/Lightbox.js';

    protected $secure_key;
    protected $merchant_id;
    protected $terminal_id;
    protected $return_url;

    /**
     * Payment checkout view.
     *
     * @return Factory|View|null
     */
    public function checkoutForm()
    {
        $dateTime = date('YmdHis');
        $string = "Amount=$this->amount&DateTimeLocalTrxn=$dateTime&MerchantId=$this->merchant_id&MerchantReference=$this->transaction_id&TerminalId=$this->terminal_id";
        $secureHash = hash_hmac('sha256', $string, hex2bin($this->secure_key));
        $secureHash = strtoupper($secureHash);

        return view('payment::upg', [
            'lightbox_js' => $this->lightbox_js,
            'mID' => $this->merchant_id,
            'tID' => $this->terminal_id,
            'secureHash' => $secureHash,
            'amount' => $this->amount,
            'order_id' => $this->transaction_id,
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
        $responseData = Arr::except($request->all(), [
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

        $responseData['MerchantId'] = $this->merchant_id;
        $responseData['TerminalId'] = $this->terminal_id;
        ksort($responseData);
        $string = [];
        foreach ($responseData as $key => $value) {
            $string[] = "$key=$value";
        }
        $string = implode('&', $string);

        $generatedHash = hash_hmac('sha256', $string, hex2bin($this->secure_key));
        $generatedHash = strtoupper($generatedHash);

        $isSuccess = $generatedHash == $request->get('SecureHash') && $request->get('success') == 0;

        $data = [
            'payment_reference'  => $request->get('SystemReference'),
            'payment_transaction_id' => $responseData['transaction_id'],
        ];

        return [
            'success' => $isSuccess,
            'message' => $isSuccess ? 'Payment completed successfully' : 'Transaction did not completed',
            'data'    => $isSuccess ? $data : [],
        ];
    }

    /**
     * Verify if payment status from gateway.
     *
     * @param int $payment_order_id
     * @return array
     *
     * @throws \RuntimeException
     */
    public function verify(int $payment_order_id): array
    {
        throw new \RuntimeException('Not implemented yet.');
    }
}
