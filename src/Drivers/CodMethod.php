<?php

namespace Shabayek\Payment\Drivers;

use Illuminate\Http\Request;
use Shabayek\Payment\Contracts\PaymentMethodContract;

class CodMethod extends Method implements PaymentMethodContract
{
    /**
     * Set credentials of payment methods.
     *
     * @return void
     */
    protected function setCredentials($credentials)
    {
        // Implementation set credentials of payment methods.
    }

    /**
     * Purchase with paymant mwthod and get redirect url.
     *
     * @return string
     */
    public function purchase(): string
    {
        return "";
    }

    /**
     * Pay with payment method.
     *
     * @param Request $request
     *
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
     * Verify if payment status from gateway
     *
     * @param int $payment_order_id
     * @return array
     */
    public function verify(int $payment_order_id): array
    {
        return [
            'success' => true,
            'message' => "Verify payment status successfully",
            'data' => []
        ];
    }
}
