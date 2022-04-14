<?php

if (! function_exists('fakeCustomer')) {
    /**
     * Get customer fake model.
     *
     * @return array
     */
    function fakeCustomer(): \Shabayek\Payment\Tests\Fixtures\User
    {
        $user = new \Shabayek\Payment\Tests\Fixtures\User();
        $user->first_name = 'John';
        $user->last_name = 'Doe';
        $user->email = 'test@payment.com';
        $user->phone = '+966123456789';

        return $user;
    }
}
if (! function_exists('fakeItems')) {
    /**
     * Get fake items.
     *
     * @return array
     */
    function fakeItems()
    {
        return [
            'id'          => '111',
            'name'        => 'Product name',
            'description' => 'Product description',
            'price'       => 150,
            'quantity'    => 1,
        ];
    }
}
