<?php

namespace Shabayek\Payment\Contracts;

use Illuminate\Http\Request;

interface PaymentMethodContract
{
    /**
     * Pay with payment method.
     *
     * @param  Request  $request
     * @return array
     */
    public function pay(Request $request): array;

    /**
     * Verify if payment status from gateway.
     *
     * @param  int  $payment_order_id
     * @return array
     */
    public function verify(int $payment_order_id): array;
}
