<?php

namespace Shabayek\Payment\Drivers;

use Shabayek\Payment\Contracts\PaymentMethodContract;

class CodMethod extends Method implements PaymentMethodContract
{
    /**
     * COD Method constructor.
     *
     * @param Array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }
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
     * Complete payment
     *
     * @return array
     */
    public function pay($requestData)
    {
        return [
            'success' => true,
            'message' => "Cod payment completed successfully",
            'data' => []
        ];
    }
}
