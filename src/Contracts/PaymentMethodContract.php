<?php

namespace Shabayek\Payment\Contracts;

use Illuminate\Http\Request;

interface PaymentMethodContract
{
    /**
     * Purchase with paymant mwthod and get redirect url.
     *
     * @return string
     */
    public function purchase();

    /**
     * Pay with payment method.
     *
     * @param Request $request
     *
     * @return array
     */
    public function pay(Request $request);
}
