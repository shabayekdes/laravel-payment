<?php

namespace Shabayek\Payment\Contracts;

/**
 * CustomerContract interface
 * @author Esmail Shabayek <esmail.shabayek@gmail.com>
 * @package Shabayek\Payment\Contracts\CustomerContract
 */
interface CustomerContract
{
    /**
     * Set customer's details.
     *
     * @return array
     */
    public function customerDetails(): array;
}
