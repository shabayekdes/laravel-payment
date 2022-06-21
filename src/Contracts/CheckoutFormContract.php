<?php

namespace Shabayek\Payment\Contracts;

interface CheckoutFormContract extends PaymentMethodContract
{
    /**
     * Payment checkout view.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|null
     */
    public function checkoutForm();
}
