<?php

namespace Shabayek\Payment\Contracts;

/**
 * AddressContract interface
 * @author Esmail Shabayek <esmail.shabayek@gmail.com>
 * @package Shabayek\Payment\Contracts\AddressContract
 */
interface AddressContract
{
    /**
     * Set address's details.
     *
     * @return array
     */
    public function addressDetails(): array;
}
