<?php

namespace Shabayek\Payment\Drivers;

use Illuminate\Http\Request;
use Shabayek\Payment\Contracts\PaymentMethodContract;

/**
 * PayfortMethod class.
 *
 * @author Esmail Shabayek <esmail.shabayek@gmail.com>
 */
class PayfortMethod extends AbstractMethod implements PaymentMethodContract
{
    /**
     * Purchase with payment method and get redirect url.
     *
     * @return string|null
     */
    public function purchase()
    {
        // write logic here

        return null;
    }

    /**
     * Payment checkout view.
     *
     * @return void
     */
    public function checkoutForm()
    {
        $currency = config('paymob.currency');
        $parameters_array = [
            'command'             => $this->command,
            'access_code'         => $this->access_code,
            'merchant_identifier' => $this->merchant_id,
            'merchant_reference' => $this->transaction_id,

            'amount'              => $this->amount,

            'currency'            => config('paymob.currency'),
            'language'            => config('paymob.language'),

            'customer_email'      => $this->getCustomerDetails('email'),

            'return_url'          => $this->return_url,
            'customer_ip'         => $this->customer_ip,
            'customer_first_name' => $this->customer_first_name,
            'customer_last_name'  => $this->customer_last_name,
            'customer_phone_no'   => $this->customer_phone_no,
        ];
        if (isset($parameters_array['amount']) && isset($parameters_array['currency'])) {
            $parameters_array['amount'] = $this->convertFortAmount($this->amount, $currency);
        }

        $parameters_array['signature'] = $this->calculateSignature($parameters_array);
        $action = $this->_url;

        return view('payfort.request', compact(['action', 'parameters_array']));
    }

    /**
     * Pay with payment method.
     *
     * @param  Request  $request
     * @return array
     */
    public function pay(Request $request): array
    {
        // write logic here

        return [
            'success' => true,
            'message' => 'Payment completed successfully',
            'data' => [],
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
        // write logic here

        return [
            'success' => true,
            'message' => 'Verify payment status successfully',
            'data' => [],
        ];
    }

    /**
     * Calculate signature.
     *
     * @param  array  $arrData
     * @param  string  $signType
     * @return string
     */
    private function calculateSignature($arrData, $signType = 'request')
    {
        $shaString = '';
        ksort($arrData);
        foreach ($arrData as $k => $v) {
            $shaString .= "$k=$v";
        }

        if ($signType == 'request') {
            $shaString = $this->sha_request.$shaString.$this->sha_request;
        } else {
            $shaString = $this->sha_response.$shaString.$this->sha_response;
        }

        return hash($this->sha_type, $shaString);
    }

    /**
     * Convert Amount with decimal points.
     *
     * @param  float  $amount
     * @param  string  $currencyCode
     * @return float
     */
    private function convertFortAmount($amount, $currencyCode)
    {
        $decimalPoints = $this->getCurrencyDecimalPoints($currencyCode);

        return round($amount, $decimalPoints) * (pow(10, $decimalPoints));
    }

    /**
     * @param  string  $currency
     * @param int
     */
    public function getCurrencyDecimalPoints($currency)
    {
        $decimalPoint = 2;
        $arrCurrencies = [
            'JOD' => 3,
            'KWD' => 3,
            'OMR' => 3,
            'TND' => 3,
            'BHD' => 3,
            'LYD' => 3,
            'IQD' => 3,
        ];
        if (isset($arrCurrencies[$currency])) {
            $decimalPoint = $arrCurrencies[$currency];
        }

        return $decimalPoint;
    }
}
