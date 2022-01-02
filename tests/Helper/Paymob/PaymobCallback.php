<?php
namespace Shabayek\Payment\Tests\Helper\Paymob;

class PaymobCallback
{
    /**
     * Return the processes paymob callback
     *
     * @return array
     */
    public static function processesCallback()
    {
        return [
            "obj" => [
                "id" => 19766242,
                "pending" => false,
                "amount_cents" => 102797,
                "success" => true,
                "is_auth" => false,
                "is_capture" => false,
                "is_standalone_payment" => true,
                "is_voided" => false,
                "is_refunded" => false,
                "is_3d_secure" => true,
                "integration_id" => 6746,
                "profile_id" => 4217,
                "has_parent_transaction" => false,
                "order" => [
                    "id" => 24826928,
                    "created_at" => "2021-12-09T17:08:03.715077",
                    "delivery_needed" => false,
                    "merchant" => [
                        "id" => 4217,
                        "created_at" => "2019-09-23T12:24:20.767230",
                        "phones" => [
                            "01019525432"
                        ],
                        "company_emails" => [
                            "mostafashahba@mobilaty.com"
                        ],
                        "company_name" => "Mobilaty.com",
                        "state" => null,
                        "country" => "EGY",
                        "city" => "Cairo",
                        "postal_code" => null,
                        "street" => null
                    ],
                    "collector" => null,
                    "amount_cents" => 102797,
                    "shipping_data" => [
                        "id" => 16387336,
                        "first_name" => "Esmail",
                        "last_name" => "Esmail",
                        "street" => "test",
                        "building" => "test",
                        "floor" => "test",
                        "apartment" => "10",
                        "city" => "Cairo",
                        "state" => "Maadi",
                        "country" => "EG",
                        "email" => "esmail2@el-dokan.com",
                        "phone_number" => "01097072481",
                        "postal_code" => "NA",
                        "extra_description" => null,
                        "shipping_method" => "UNK",
                        "order_id" => 24826928,
                        "order" => 24826928
                    ],
                    "shipping_details" => null,
                    "currency" => "EGP",
                    "is_payment_locked" => false,
                    "is_return" => false,
                    "is_cancel" => false,
                    "is_returned" => false,
                    "is_canceled" => false,
                    "merchant_order_id" => "23789-18099",
                    "wallet_notification" => null,
                    "paid_amount_cents" => 102797,
                    "notify_user_with_email" => false,
                    "items" => [
                        [
                            "name" => "Huawei Band 6 - Graphite Black",
                            "description" => "14 days of battery life1.43&#34; AMOLED display5 ATM water-resistanceOptical heart rate sensor",
                            "amount_cents" => 99900,
                            "quantity" => 1
                        ]
                    ],
                    "order_url" => "https://accept.paymobsolutions.com/i/b6MS3",
                    "commission_fees" => 0,
                    "delivery_fees_cents" => 0,
                    "delivery_vat_cents" => 0,
                    "payment_method" => "tbc",
                    "merchant_staff_tag" => null,
                    "api_source" => "OTHER",
                    "pickup_data" => null,
                    "delivery_status" => [],
                    "data" => []
                ],
                "created_at" => "2021-12-09T17:08:11.723266",
                "transaction_processed_callback_responses" => [],
                "currency" => "EGP",
                "source_data" => [
                    "pan" => "0008",
                    "sub_type" => "MasterCard",
                    "tenure" => null,
                    "type" => "card"
                ],
                "api_source" => "IFRAME",
                "terminal_id" => null,
                "merchant_commission" => 0,
                "is_void" => false,
                "is_refund" => false,
                "data" => [
                    "acq_response_code" => "00",
                    "txn_response_code" => "APPROVED",
                    "order_info" => "24826928",
                    "transaction_no" => "123456789",
                    "authorize_id" => "210771",
                    "gateway_integration_pk" => 6746,
                    "amount" => 102797,
                    "card_type" => "MASTERCARD",
                    "receipt_no" => "134315210771",
                    "authorised_amount" => 1027.97,
                    "created_at" => "2021-12-09T15:08:23.044083",
                    "avs_result_code" => null,
                    "card_num" => "512345xxxxxx0008",
                    "migs_result" => "SUCCESS",
                    "avs_acq_response_code" => "00",
                    "migs_transaction" => [
                        "currency" => "EGP",
                        "type" => "PAYMENT",
                        "receipt" => "134315210771",
                        "acquirer" => [
                            "batch" => 20211209,
                            "transactionId" => "123456789",
                            "settlementDate" => "2021-12-09",
                            "date" => "1209",
                            "id" => "BMNF_S2I",
                            "merchantId" => "MERCH_C_25P",
                            "timeZone" => "+0200"
                        ],
                        "frequency" => "SINGLE",
                        "amount" => 1027.97,
                        "source" => "INTERNET",
                        "id" => "19766242",
                        "terminal" => "BQMS2I01",
                        "authorizationCode" => "210771"
                    ],
                    "secure_hash" => null,
                    "captured_amount" => 1027.97,
                    "klass" => "MigsPayment",
                    "batch_no" => 20211209,
                    "refunded_amount" => 0,
                    "merchant" => "TESTMERCH_C_25P",
                    "currency" => "EGP",
                    "message" => "Approved",
                    "merchant_txn_ref" => "19766242",
                    "migs_order" => [
                        "acceptPartialAmount" => false,
                        "currency" => "EGP",
                        "status" => "CAPTURED",
                        "id" => "24826928",
                        "totalAuthorizedAmount" => 1027.97,
                        "totalRefundedAmount" => 0,
                        "amount" => 1027.97,
                        "totalCapturedAmount" => 1027.97,
                        "creationTime" => "2021-12-09T15:08:22.800Z"
                    ]
                ],
                "is_hidden" => false,
                "payment_key_claims" => [
                    "currency" => "EGP",
                    "integration_id" => 6746,
                    "lock_order_when_paid" => false,
                    "billing_data" => [
                        "state" => "Maadi",
                        "street" => "test",
                        "first_name" => "Esmail",
                        "email" => "esmail2@el-dokan.com",
                        "floor" => "test",
                        "country" => "EG",
                        "apartment" => "10",
                        "building" => "test",
                        "city" => "Cairo",
                        "last_name" => "Esmail",
                        "extra_description" => "NA",
                        "phone_number" => "01097072481",
                        "postal_code" => "NA"
                    ],
                    "pmk_ip" => "3.128.58.245",
                    "order_id" => 24826928,
                    "exp" => 1639066083,
                    "amount_cents" => 102797,
                    "user_id" => 4708
                ],
                "error_occured" => false,
                "is_live" => false,
                "other_endpoint_reference" => null,
                "refunded_amount_cents" => 0,
                "source_id" => -1,
                "is_captured" => false,
                "captured_amount" => 0,
                "merchant_staff_tag" => null,
                "owner" => 4708,
                "parent_transaction" => null
            ],
            "type" => "TRANSACTION",
            "hmac" => "7dc8cda513fe5119adb7b7737bb50251ea41d45ad19a191ded419ea3dc3af358f81f714e24d670329784278feb689eb8eca946897e2b48dd2de57fab1e5c38e0"
        ];
    }
    /**
     * Return the processes paymob callback
     *
     * @return array
     */
    public static function responseCallback()
    {
        return [
            "owner" => "4708",
            "txn_response_code" => "AUTHENTICATION_FAILED",
            "source_data_sub_type" => "MasterCard",
            "is_standalone_payment" => "true",
            "profile_id" => "4217",
            "order" => "24827227",
            "source_data_type" => "card",
            "created_at" => "2021-12-09T17:10:06.099493",
            "has_parent_transaction" => "false",
            "refunded_amount_cents" => "0",
            "is_refunded" => "false",
            "merchant_order_id" => "23790-69578",
            "is_3d_secure" => "true",
            "success" => "true",
            "is_refund" => "false",
            "acq_response_code" => "-",
            "data_message" => "AUTHENTICATION_FAILED",
            "captured_amount" => "0",
            "pending" => "false",
            "is_void" => "false",
            "error_occured" => "false",
            "currency" => "EGP",
            "is_auth" => "false",
            "integration_id" => "6746",
            "amount_cents" => "102797",
            "is_voided" => "false",
            "source_data_pan" => "0008",
            "merchant_commission" => "0",
            "is_capture" => "false",
            "hmac" => "91718408c8962be7b17b8397e993668677f3949345b887e228d61a3eed1003ebfca18297589c544670257787a89e4ffd89b6a91ae1bab346cfe8186b5eb342f6",
            "id" => "19766521"
         ];
    }
}