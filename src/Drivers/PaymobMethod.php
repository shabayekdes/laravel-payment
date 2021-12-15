<?php

namespace Shabayek\Payment\Drivers;

use GuzzleHttp\Client;
use Shabayek\Payment\Contracts\PaymentMethodContract;

/**
 * PaymobMethod class
 * @package Shabayek\Payment\Drivers\PaymobMethod
 */
class PaymobMethod extends Method implements PaymentMethodContract
{
    /**
     * Payping Client.
     *
     * @var object
     */
    protected $client;
    /**
     * PaymobMethod constructor.
     *
     * @param Array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->setCredentials($config['credentials']);
        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                // 'Accept' => 'application/json',
            ],
        ]);
    }
    /**
     * Set credentials of paymob payment
     *
     * @return void
     */
    protected function setCredentials($credentials)
    {
        $this->url = "https://accept.paymobsolutions.com/api/";
        $this->iframeID = $credentials['iframe_id'];
        $this->integrationID = $credentials['integration_id'];
        $this->authApiKey = $credentials['api_key'];
        $this->merchantID = $credentials['merchant_id'];
        $this->hmacHash = $credentials['hmac_hash'];
    }
    /**
     * Get redirect payement url
     *
     * @return void
     */
    public function purchase()
    {
        $token = $this->getAuthenticationToken();
        $orderCreation = $this->orderCreation($token);
        $paymentKey = $this->paymentKeyRequest($token, $orderCreation['id']);

        return "{$this->url}acceptance/iframes/{$this->iframeID}?payment_token={$paymentKey}";
    }
    /**
     * Complete payment
     *
     * @return array
     */
    public function pay($requestData)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // The request is using the POST method
            $callback = $this->processesCallback($requestData['obj']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // The request is using the GET method
            $callback = $this->responseCallBack($requestData);
        }

        $isSuccess = false;
        try {
            if ($callback['transaction_status']) {
                $downPaymentInfo = [];
                if ($this->isInstallment()) {
                    $orderData = $this->getOrderData($callback['paymob_order_id']);

                    $downPaymentInfo = $this->calculateInstallmentFees($orderData);
                }

                $callback['items'] = $this->items;
                $callback['down_payment_info'] = $downPaymentInfo;

                $isSuccess = true;
                $message = "Success";
            } else {
                $message = "Transaction did not completed";
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
        }

        return [
            'success' => $isSuccess,
            'message' => $message,
            'data' => $isSuccess ? $callback : []
        ];
    }
    /**
     * Authentication Request
     *
     * @return string
     */
    private function getAuthenticationToken()
    {
        $postData = ['api_key' => $this->authApiKey];

        try {
            $response = $this->client->post("{$this->url}auth/tokens", [
                'body' => json_encode($postData)
            ]);

            $result = json_decode($response->getBody(), true);
            return $result['token'];
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * Order registration API
     *
     * @param string $token
     * @return object
     */
    private function orderCreation($token)
    {
        $postData = [
            'auth_token' => $token,
            'delivery_needed' => false,
            'merchant_order_id' => $this->transaction_id . "-" . rand(10000, 99999),
            'merchant_id' => $this->merchantID,
            'amount_cents' => (int) $this->mount * 100,
            'currency' => "EGP",
            'items' => $this->items,
            'shipping_data' => [
                "first_name" => $this->customer['first_name'],
                "last_name" => $this->customer['last_name'],
                "email" => $this->customer['email'],
                "phone_number" => $this->customer['phone'],
            ]
        ];
        try {
            $response = $this->client->post("{$this->url}ecommerce/orders", [
                'body' => json_encode($postData)
            ]);
            return json_encode($response->getBody());
        } catch (\Exception $e) {
            throw new \Exception("Order not created success in paymob #" . $e->getMessage());
        }
    }
    /**
     * Get payment key request
     *
     * @param int $orderPayId
     * @return string
     */
    private function paymentKeyRequest($token, $orderPayId)
    {
        $postData = [
            'auth_token' => $token,
            'amount_cents' => (int) $this->mount * 100,
            'expiration' => 3600,
            'order_id' => $orderPayId,
            'currency' => "EGP",
            'integration_id' => $this->integrationID,
            'billing_data' => [
                "first_name" => $this->customer['first_name'],
                "last_name" => $this->customer['last_name'] ?? 'NA',
                "phone_number" => $this->customer['phone'],
                "email" => $this->customer['email'],
                "apartment" => $this->address['apartment'] ?? "NA",
                "floor" => $this->address['floor'] ?? "NA",
                "city" => $this->address['city'] ?? "NA",
                "state" => $this->address['state'] ?? "NA",
                "street" => $this->address['street'] ?? "NA",
                "building" => $this->address['building'] ?? "NA",
                "country" => "EG",
            ],
        ];

        try {
            $response = $this->client->post("{$this->url}acceptance/payment_keys", [
                'body' => json_encode($postData)
            ]);

            $result = json_decode($response->getBody(), true);
            return $result['token'];
        } catch (\Exception $e) {
            throw new \Exception("Payment key request not created success in paymob #" . $e->getMessage());
        }
    }
       /**
     * Record processes callback - POST request
     *
     * @param array $requestData
     * @return array
     */
    private function processesCallback($requestData): array
    {
        $hmacKeys = [
            "amount_cents",
            "created_at",
            "currency",
            "error_occured",
            "has_parent_transaction",
            "id",
            "integration_id",
            "is_3d_secure",
            "is_auth",
            "is_capture",
            "is_refunded",
            "is_standalone_payment",
            "is_voided",
            "order.id",
            "owner",
            "pending",
            "source_data.pan",
            "source_data.sub_type",
            "source_data.type",
            "success"
        ];
        $hmac = $this->calculateHmac($requestData, $hmacKeys);
        if ($hmac != $requestData['hmac']) {
            throw new Exception('HMAC is not valid');
        }

        $transaction_status = $requestData['success'] ?? false;

        return [
            'payment_order_id' => $requestData['order']['id'],
            'payment_transaction_id' => $requestData['id'],
            'status' => $transaction_status === true
        ];
    }
    /**
     * Record response callback - GET request
     *
     * @param array $requestData
     * @return array
     */
    private function responseCallBack($requestData)
    {
        $hmacKeys = [
            "amount_cents",
            "created_at",
            "currency",
            "error_occured",
            "has_parent_transaction",
            "id",
            "integration_id",
            "is_3d_secure",
            "is_auth",
            "is_capture",
            "is_refunded",
            "is_standalone_payment",
            "is_voided",
            "order",
            "owner",
            "pending",
            "source_data_pan",
            "source_data_sub_type",
            "source_data_type",
            "success"
        ];
        $hmac = $this->calculateHmac($requestData, $hmacKeys);
        if ($hmac != $requestData['hmac']) {
            throw new Exception('HMAC is not valid');
        }

        $transaction_status = $requestData['success'];
        return [
            'paymob_order_id' => $requestData['order'],
            'payment_transaction_id' => $requestData['id'],
            'status' => $transaction_status === "true"
        ];
    }

    /**
     * HMAC Calculation
     *
     * @param array $requestData
     * @param array $hmacKeys
     * @return string
     */
    private function calculateHmac(array $requestData, array $hmacKeys): string
    {
        $requestValues = [];
        foreach ($hmacKeys as $key) {
            $value = $requestData[$key];
            if ($value === true) {
                $value = "true";
            } elseif ($value === false) {
                $value = "false";
            }
            $requestValues[] = $value;
        }

        $sig = hash_hmac('sha512', implode('', $requestValues), $this->hmacHash);
        return $sig;
    }
    /**
     * Get order detials
     *
     * @param [type] $id
     * @return void|object
     */
    private function getOrderData($id)
    {
        try {
            $token = $this->Authentication();
            $response = $this->client->get("{$this->url}acceptance/transactions/{$id}", [
                'headers' => [
                    'authorization' => $token
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            return $result;
        } catch (\Exception $e) {
            throw new \Exception("Get order data failed in paymob #" . $e->getMessage());
        }
        return [];
    }
    /**
     * Calculate the installment fees.
     *
     * @param [type] $orderDetials
     * @return array
     */
    private function calculateInstallmentFees($orderDetials)
    {
        $result = [];

        $driver = $this->config['driver'];

        switch ($driver) {
            case "valu":
                $result['down_payment'] = data_get($orderDetials, 'data.down_payment') ?? 0;
                $result['admin_fees'] = 0; // $orderDetials->data->purchase_fees ?? 0; ##Change happened in valu response
                break;
            case "shahry":
                $result['down_payment'] = data_get($orderDetials, 'data.shahry_order.down_payment') ?? 0;
                $result['admin_fees'] = data_get($orderDetials, 'data.shahry_order.administrative_fees') ?? 0;
                break;
            case "souhoola":
                $result['down_payment'] = data_get($orderDetials, 'data.installment_info.downpaymentValue') ?? 0;
                $result['admin_fees'] = data_get($orderDetials, 'data.installment_info.adminFees') ?? 0;
                break;
            case "get_go":
                $result['down_payment'] = data_get($orderDetials, 'data.down_payment') ?? 0;
                $result['admin_fees'] = 0;
                break;
            default:
                $result['down_payment'] = 0;
                $result['admin_fees'] = 0;
                break;
        }
        return $result;
    }
}
