<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default payment "provider" that will be used on
    | requests.
    |
    |
    */

    'default' => env('PAYMENT_DEFAULT_DRIVER', 'cod'),

    'currency' => env('GATEWAY_CURRENCY', 'EGP'),
    'country'  => env('GATEWAY_COUNTRY', 'EG'),

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
         * UPG Meza
         */
        8 => [
            'provider'       => 'upg',
            'gateway'        => 'meeza-upg',
            'is_active'      => false,
            'is_online'      => true,
            'is_installment' => false,
            'name'           => 'Meeza',
            'logo'           => 'images/credit_payment.png',
            'credentials'    => [
                'secure_key'  => env('UPG_MEEZA_SECURE_KEY'),
                'merchant_id' => env('UPG_MEEZA_MERCHANT_ID'),
                'terminal_id' => env('UPG_MEEZA_TERMINAL_ID'),
                'return_url'  => env('UPG_MEEZA_RETURN_URL'),
            ],
        ],

    ],

];
