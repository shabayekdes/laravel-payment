<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default payment "provider" that will be used on
    | requests. By default, we will use the "cod" payment driver which does
    |
    */

    'default' => env('PAYMENT_DEFAULT_DRIVER', 'cod'),

    /*
    |--------------------------------------------------------------------------
    | Payment Environment
    |--------------------------------------------------------------------------
    |
    | This option controls the default payment language that will be used
    |
    */
    'test_env' => env('PAYMENT_TEST_ENV', true),

    /*
    |--------------------------------------------------------------------------
    | Payment Currency
    |--------------------------------------------------------------------------
    |
    | This option controls the default payment currency that will be used
    |
    */

    'currency' => env('PAYMENT_CURRENCY', 'EGP'),

    /*
    |--------------------------------------------------------------------------
    | Payment Country
    |--------------------------------------------------------------------------
    |
    | This option controls the default payment country that will be used
    |
    */
    'country' => env('PAYMENT_COUNTRY', 'EG'),

    /*
    |--------------------------------------------------------------------------
    | Payment Stores
    |--------------------------------------------------------------------------
    |
    |
    */

    'stores' => [
        /**
         * Cash On Delivery.
         */
        1 => [
            'provider'       => 'cod',
            'gateway'        => 'cod',
            'is_active'      => true,
            'is_online'      => false,
            'is_installment' => false,
            'name'           => 'Cash On Delivery',
            'logo'           => 'images/payment_methods/cash_payment.png',
            'credentials'    => [],
        ],
        /**
         * Credit / Debit card Paymob.
         */
        2 => [
            'provider'       => 'paymob',
            'gateway'        => 'visa-paymob',
            'is_active'      => true,
            'is_online'      => true,
            'is_installment' => false,
            'name'           => 'Credit / Debit card',
            'logo'           => 'images/credit_payment.png',
            'credentials'    => [
                'api_key'        => env('PAYMOB_API_KEY'),
                'hmac_hash'      => env('PAYMOB_HMAC_HASH'),
                'merchant_id'    => env('PAYMOB_MERCHANT_ID'),
                'iframe_id'      => env('PAYMOB_CARD_IFRAME_ID'),
                'integration_id' => env('PAYMOB_CARD_INTEGRATION_ID'),
            ],
        ],
        /**
         * Credit / Debit card QNB Mastercard.
         */
        3 => [
            'provider'       => 'mastercard',
            'gateway'        => 'visa-qnb',
            'is_active'      => true,
            'is_online'      => true,
            'is_installment' => false,
            'name'           => 'Credit / Debit card QNB',
            'logo'           => 'images/credit_payment.png',
            'credentials'    => [
                'username'     => env('QNB_USERNAME'),
                'password'     => env('QNB_PASSWORD'),
                'base_url'     => env('QNB_BASE_URL'),
                'callback_url' => env('QNB_CALLBACK_URL'),
                'checkout_js'  => env('QNB_CHECKOUT_JS'),
                'merchant_id'  => env('QNB_MERCHANT_ID'),
            ],
        ],
        /**
         * Credit / Debit card Paytabs.
         */
        4 => [
            'provider'       => 'paytabs',
            'gateway'        => 'visa-paytabs',
            'is_active'      => true,
            'is_online'      => true,
            'is_installment' => false,
            'name'           => 'Credit / Debit card Paytabs',
            'logo'           => 'images/credit_payment.png',
            'credentials'    => [
                'base_url'     => 'https://secure-egypt.paytabs.com/',
                'callback_url' => env('PAYTABS_CALLBACK_URL'),
                'server_key'   => env('PAYTABS_SERVER_KEY'),
                'profile_id'   => env('PAYTABS_PROFILE_ID'),
            ],
        ],
        /**
         * Credit / Debit card Paytabs.
         */
        5 => [
            'provider'       => 'payfort',
            'gateway'        => 'visa-payfort',
            'is_active'      => true,
            'is_online'      => true,
            'is_installment' => false,
            'name'           => 'Credit / Debit card Payfort',
            'logo'           => 'images/credit_payment.png',
            'credentials'    => [
                'command'             => env('PAYFORT_COMMAND', 'PURCHASE'),    // PURCHASE, AUTHORIZATION
                'sha_type'            => env('PAYFORT_SHA_TYPE', 'SHA-256'),
                'access_code'         => env('PAYFORT_ACCESS_CODE'),
                'merchant_identifier' => env('PAYFORT_MERCHANT_IDENTIFIER'),
            ],
        ],
    ],

];
