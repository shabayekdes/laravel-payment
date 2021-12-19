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
     * Get redirect payement url
     *
     * @return void
     */
    public function purchase()
    {
        return null;
    }
    /**
     * Pay with payment method.
     *
     * @param Request $request
     * @return array
     */
    public function pay(Request $request)
    {
        return [
            'success' => true,
            'message' => "Cod payment completed successfully",
            'data' => []
        ];
    }
}
