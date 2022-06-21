<?php

namespace Shabayek\Payment\Contracts;

interface PurchaseContract extends PaymentMethodContract
{
    /**
     * Purchase with payment method and get redirect url.
     *
     * @return string|null
     */
    public function purchase();
}
