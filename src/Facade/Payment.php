<?php

namespace Shabayek\Payment\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Payment Facade.
 *
 * @author Esmail Shabayek
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
