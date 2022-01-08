<?php

namespace Shabayek\Payment\Contracts;

/**
 * CustomerContract interface.
 *
 * @author Esmail Shabayek <esmail.shabayek@gmail.com>
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
