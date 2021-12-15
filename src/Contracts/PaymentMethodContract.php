<?php

namespace Shabayek\Payment\Contracts;

interface PaymentMethodContract
{
    /**
     * Purchase with paymant mwthod and get redirect url
     *
     * @return string
     */
    public function purchase();

    /**
     * Pay with payment method.
     *
     * @return array
     */
    public function pay($requestData);
}
