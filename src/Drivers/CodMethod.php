<?php

namespace Shabayek\Payment\Drivers;

use Illuminate\Http\Request;
use Shabayek\Payment\Contracts\PaymentMethodContract;

class CodMethod extends AbstractMethod implements PaymentMethodContract
{
    /**
     * Purchase with paymant mwthod and get redirect url.
     *
     * @return string|null
     */
    public function purchase()
    {
        return null;
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
}
