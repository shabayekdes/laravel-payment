<?php

namespace Shabayek\Payment\Drivers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Shabayek\Payment\Contracts\PaymentMethodContract;
use Shabayek\Payment\Contracts\PurchaseContract;
use Shabayek\Payment\Enums\Gateway;

/**
 * Paytabs Method class.
 *
 * @author Esmail Shabayek <esmail.shabayek@gmail.com>
 */
class PaytabsMethod extends AbstractMethod implements PurchaseContract
{
    protected $base_url;
    protected $server_key;
    protected $profile_id;
    protected $callback_url;

    /**
     * Purchase with payment method and get redirect url.
     *
     * @return string|null
     */
    public function purchase()
    {
        return $this->paymentRequest();
    }

    /**
     * Pay with payment method.
     *
     * @param  Request  $request
     * @return array
     */
    public function pay(Request $request): array
    {
        $response_status = $request->input('payment_result.response_status') ?? $request->input('respStatus');
        $transaction_status = $response_status === Gateway::PAYTABS_RESPONSE_SUCCESS;

        return [
            'success' => $transaction_status,
            'message' => $transaction_status ? 'Payment Successfully' : 'Payment Failed',
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
        $success = $this->paymentQuery($payment_order_id);

        return [
            'success' => $success,
            'message' => $success ? 'Verify payment status successfully' : 'Verify payment status failed',
            'data' => [],
        ];
    }

    /**
     * Payment request api call.
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    private function paymentRequest()
    {
        $requestBody = $this->requestBody();

        $response = Http::withHeaders([
            'authorization' => $this->server_key,
        ])->post($this->base_url.'payment/request', $requestBody);

        $redirectUrl = null;
        if ($response->ok()) {
            $result = $response->json();
            $redirectUrl = $result['redirect_url'];

            $this->payment_reference = $result['tran_ref'];
        }

        return $redirectUrl;
    }

    /**
     * Payment query api call.
     *
     * @param  string|int  $payment_order_id
     * @return bool
     */
    private function paymentQuery($payment_order_id)
    {
        $requestBody = [
            'profile_id' => $this->profile_id,
            'tran_ref' => $payment_order_id,
        ];

        $response = Http::withHeaders([
            'authorization' => $this->server_key,
        ])->post($this->base_url.'payment/query', $requestBody);

        $result = $response->json();

        if ($response->ok() && $response) {
            return data_get($result, 'payment_result.response_status') == Gateway::PAYTABS_RESPONSE_SUCCESS;
        }
        $this->setErrors('Paytabs method error '.$this->getErrors($result['code'] ?? null));

        return false;
    }

    /**
     * Handle payment body request.
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    private function requestBody()
    {
        $cartDescription = $this->getItems();

        return [
            'profile_id'       => $this->profile_id,
            'tran_type'        => 'sale',
            'tran_class'       => 'ecom',
            'cart_id'          => (string) $this->transaction_id,
            'cart_description' => $cartDescription,
            'cart_currency'    => config('paymob.currency'),
            'cart_amount'      => $this->amount,
            'callback'         => $this->callback_url,
            'return'           => $this->callback_url,
            'hide_shipping'    => true,
            'customer_details' => [
                'name'    => $this->getCustomerDetails('full_name'),
                'email'   => $this->getCustomerDetails('email') ?? 'no email',
                'phone'   => $this->getCustomerDetails('phone'),
                'street1' => $this->getBillingDetails('street'),
                'city'    => $this->getBillingDetails('city'),
                'zip'     => $this->getBillingDetails('zip'),
                'country' => config('paymob.currency'),
            ],
        ];
    }

    /**
     * Get items.
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    private function getItems()
    {
        if (empty($this->items)) {
            throw new \InvalidArgumentException('Items not set.');
        }

        return collect($this->items)->mapWithKeys(function ($item) {
            return [
                $item['id'] => "{$item['id']} × {$item['quantity']} ({$item['name']})",
            ];
        })->implode(', ');
    }
}
