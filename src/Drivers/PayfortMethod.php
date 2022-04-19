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
        // write logic here

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
        // write logic here

        return [
            'success' => true,
            'message' => 'Payment completed successfully',
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
        // write logic here

        return [
            'success' => true,
            'message' => 'Verify payment status successfully',
            'data' => [],
        ];
    }
}
