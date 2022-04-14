<?php

namespace Shabayek\Payment\Drivers;

use Illuminate\Http\Request;
use InvalidArgumentException;
use Illuminate\Support\Facades\Http;
use Shabayek\Payment\Contracts\PaymentMethodContract;

/**
 * PaytabsMethod class.
 * @package Shabayek\Payment\Drivers
 * @author Esmail Shabayek <esmail.shabayek@gmail.com>
 */
class PaytabsMethod extends AbstractMethod implements PaymentMethodContract
{
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
        return [
            'success' => true,
            'message' => 'Cod payment completed successfully',
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
    /**
     * Payment request api call.
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    private function paymentRequest()
    {
        $requestBody = $this->requestBody();

        $response = Http::withHeaders([
            'authorization' => $this->server_key
        ])->post($this->base_url . 'payment/request', $requestBody);

        $redirectUrl = null;
        if ($response->ok()) {
            $result = $response->json();
            $redirectUrl = $result['redirect_url'];

            $this->payment_reference = $result['tran_ref'];
        }

        return $redirectUrl;
    }
    /**
     * Handle payment body request
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    private function requestBody()
    {
        $cartDescription = $this->getItems();

        return [
            "profile_id"       => $this->profile_id,
            "tran_type"        => "sale",
            "tran_class"       => "ecom",
            "cart_id"          => (string) $this->transaction_id,
            "cart_description" => $cartDescription,
            "cart_currency"    => config('paymob.currency'),
            "cart_amount"      => $this->amount,
            "callback"         => $this->callback_url,
            "return"           => $this->callback_url,
            "hide_shipping"    => true,
            "customer_details" => [
                "name"    => $this->getCustomerDetails('full_name'),
                "email"   => $this->getCustomerDetails('email') ?? "no email",
                "phone"   => $this->getCustomerDetails('phone'),
                "street1" => $this->getBillingDetails('street'),
                "city"    => $this->getBillingDetails('city'),
                "zip"     => $this->getBillingDetails('zip'),
                "country" => config('paymob.currency'),
            ]
        ];
    }
    /**
     * Get items
     *
     * @return string
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
