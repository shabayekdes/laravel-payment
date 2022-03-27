<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default payment "driver" that will be used on
    | requests.
    |
    |
    */

    'default' => env('PAYMENT_DEFAULT_DRIVER', 'cod'),

    'currency' => env('GATEWAY_CURRENCY', 'EGP'),
    'country' => env('GATEWAY_COUNTRY', 'EG'),

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
            'driver'         => 'cod',
            'is_active'      => true,
            'is_online'      => false,
            'is_Installment' => false,
            'name_en'        => 'Cash On Delivery',
            'name_ar'        => 'الدفع عند الاستلام',
            'icon'           => 'images/payment_methods/cash_payment.png',
            'credentials'    => [],
        ],
        /**
         * Credit / Debit card Paymob.
         */
        2 => [
            'driver'         => 'paymob',
            'is_active'      => true,
            'is_online'      => true,
            'is_installment' => false,
            'name_en'        => 'Credit / Debit card',
            'name_ar'        => 'بطاقة الائتمان \ خصم مباشر',
            'icon'           => 'images/payment_methods/credit_payment.png',
            'credentials'    => [
                'api_key'        => env('PAYMOB_API_KEY'),
                'hmac_hash'      => env('PAYMOB_HMAC_HASH'),
                'merchant_id'    => env('PAYMOB_MERCHANT_ID'),
                'iframe_id'      => env('PAYMOB_CARD_IFRAME_ID'),
                'integration_id' => env('PAYMOB_CARD_INTEGRATION_ID'),
            ],
        ],
        /**
         * ValU Paymob.
         */
        3 => [
            'driver'         => 'paymob',
            'is_active'      => true,
            'is_online'      => true,
            'is_installment' => true,
            'name_en'        => 'ValU',
            'name_ar'        => 'فاليو',
            'icon'           => 'images/payment_methods/valU_payment.png',
            'credentials'    => [
                'api_key'        => env('PAYMOB_API_KEY'),
                'hmac_hash'      => env('PAYMOB_HMAC_HASH'),
                'merchant_id'    => env('PAYMOB_MERCHANT_ID'),
                'iframe_id'      => env('PAYMOB_VALU_IFRAME_ID'),
                'integration_id' => env('PAYMOB_VALU_INTEGRATION_ID'),
            ],
        ],
        /**
         * Premium Paymob.
         */
        4 => [
            'driver'         => 'paymob',
            'is_active'      => true,
            'is_online'      => true,
            'is_installment' => true,
            'name_en'        => 'Premium',
            'name_ar'        => 'بريميوم',
            'icon'           => 'images/payment_methods/premium.png',
            'credentials'    => [
                'api_key'        => env('PAYMOB_API_KEY'),
                'hmac_hash'      => env('PAYMOB_HMAC_HASH'),
                'merchant_id'    => env('PAYMOB_MERCHANT_ID'),
                'iframe_id'      => env('PAYMOB_PREMIUM_IFRAME_ID'),
                'integration_id' => env('PAYMOB_PREMIUM_INTEGRATION_ID'),
            ],
        ],
        /**
         * Visa NBE MasterCard.
         */
        5 => [
            'driver'         => 'mastercard',
            'is_active'      => true,
            'is_online'      => true,
            'is_installment' => false,
            'name_en'        => 'Visa NBE',
            'name_ar'        => 'فيزا البنك الاهلى',
            'icon'           => 'images/payment_methods/visa_nbe_payment.png',
            'credentials'    => [
                'username'    => env('MASTERCARD_NBE_USERNAME'),
                'password'    => env('MASTERCARD_NBE_PASSWORD'),
                'base_url'    => env('MASTERCARD_NBE_BASE_URL'),
                'checkout_js' => env('MASTERCARD_NBE_CHECKOUT_JS'),
                'merchant_id' => env('MASTERCARD_NBE_MERCHANT_ID'),
            ],
        ],
        /**
         * Visa QNB MasterCard.
         */
        6 => [
            'driver'         => 'mastercard',
            'class'          => \App\Classes\Payment\Drivers\MasterCardMethod::class,
            'is_active'      => true,
            'is_online'      => true,
            'is_installment' => false,
            'name_en'        => 'Credit / Debit card QNB',
            'name_ar'        => 'بطاقة الائتمان \ خصم مباشر',
            'icon'           => 'images/payment_methods/credit_payment.png',
            'credentials'    => [
                'username'    => env('MASTERCARD_QNB_USERNAME'),
                'password'    => env('MASTERCARD_QNB_PASSWORD'),
                'base_url'    => env('MASTERCARD_QNB_BASE_URL'),
                'checkout_js' => env('MASTERCARD_QNB_CHECKOUT_JS'),
                'merchant_id' => env('MASTERCARD_QNB_MERCHANT_ID'),
            ],
        ],
        /**
         * Visa Meza Upg.
         */
        7 => [
            'driver'         => 'upg',
            'is_active'      => false,
            'is_online'      => true,
            'is_installment' => false,
            'name_en'        => 'Meza',
            'name_ar'        => 'ميزه',
            'icon'           => 'images/payment_methods/meza_payment.png',
            'credentials'    => [
                'secure_key'  => env('UPG_MEZA_SECURE_KEY'),
                'merchant_id' => env('UPG_MEZA_MERCHANT_ID'),
                'terminal_id' => env('UPG_MEZA_TERMINAL_ID'),
            ],
        ],
        /**
         * Souhoola Paymob.
         */
        8 => [
            'driver'         => 'paymob',
            'is_active'      => true,
            'is_online'      => true,
            'is_installment' => true,
            'name_en'        => 'Souhoola',
            'name_ar'        => 'سهولة',
            'icon'           => 'images/payment_methods/souhoola_payment.png',
            'credentials'    => [
                'api_key'        => env('PAYMOB_API_KEY'),
                'hmac_hash'      => env('PAYMOB_HMAC_HASH'),
                'merchant_id'    => env('PAYMOB_MERCHANT_ID'),
                'iframe_id'      => env('PAYMOB_SOUHOOLA_IFRAME_ID'),
                'integration_id' => env('PAYMOB_SOUHOOLA_INTEGRATION_ID'),
            ],
        ],
        /**
         * Get Go Contact Paymob.
         */
        9 => [
            'driver'         => 'paymob',
            'is_active'      => true,
            'is_online'      => true,
            'is_installment' => true,
            'name_en'        => 'Get Go',
            'name_ar'        => 'جيت جو',
            'icon'           => 'images/payment_methods/get_go_payment.png',
            'credentials'    => [
                'api_key'        => env('PAYMOB_API_KEY'),
                'hmac_hash'      => env('PAYMOB_HMAC_HASH'),
                'merchant_id'    => env('PAYMOB_MERCHANT_ID'),
                'iframe_id'      => env('PAYMOB_GET_GO_IFRAME_ID'),
                'integration_id' => env('PAYMOB_GET_GO_INTEGRATION_ID'),
            ],
        ],
        /**
         * Shahry Paymob.
         */
        10 => [
            'driver'         => 'paymob',
            'is_active'      => true,
            'is_online'      => true,
            'is_installment' => true,
            'name_en'        => 'Shahry',
            'name_ar'        => 'شهرى',
            'icon'           => 'images/payment_methods/shahry_payment.png',
            'credentials'    => [
                'api_key'        => env('PAYMOB_API_KEY'),
                'hmac_hash'      => env('PAYMOB_HMAC_HASH'),
                'merchant_id'    => env('PAYMOB_MERCHANT_ID'),
                'iframe_id'      => env('PAYMOB_SHAHRY_IFRAME_ID'),
                'integration_id' => env('PAYMOB_SHAHRY_INTEGRATION_ID'),
            ],
        ],
        /**
         * Wallet Paymob (Vodafone Cash).
         */
        11 => [
            'driver'         => 'paymob',
            'is_active'      => true,
            'is_online'      => true,
            'is_installment' => false,
            'name_en'        => 'Vodafone Cash',
            'name_ar'        => 'فودافون كاش',
            'icon'           => 'images/payment_methods/default.png',
            'credentials'    => [
                'api_key'        => env('PAYMOB_API_KEY'),
                'hmac_hash'      => env('PAYMOB_HMAC_HASH'),
                'merchant_id'    => env('PAYMOB_MERCHANT_ID'),
                'iframe_id'      => env('PAYMOB_WALLET_IFRAME_ID'),
                'integration_id' => env('PAYMOB_WALLET_INTEGRATION_ID'),
            ],
        ],
        /**
         * Visa Paytabs.
         */
        12 => [
            'driver'         => 'paytabs',
            'is_active'      => true,
            'is_online'      => true,
            'is_installment' => false,
            'name_en'        => 'Visa Paytabs',
            'name_ar'        => 'بطاقة الائتمان \ خصم مباشر',
            'icon'           => 'images/payment_methods/visa_paytabs.png',
            'credentials'    => [
                'base_url'     => 'https://secure-egypt.paytabs.com/',
                'callback_url' => env('PAYTABS_CALLBACK_URL'),
                'server_key'   => env('PAYTABS_SERVER_KEY'),
                'profile_id'   => env('PAYTABS_PROFILE_ID'),
            ],
        ],
    ],

];
