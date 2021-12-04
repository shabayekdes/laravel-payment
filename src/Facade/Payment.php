<?php

namespace Shabayek\Payment\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Payment Facade
 * @author Esmail Shabayek
 * @package Shabayek\Payment\Facade
 */
class Payment extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'payment';
    }
}
