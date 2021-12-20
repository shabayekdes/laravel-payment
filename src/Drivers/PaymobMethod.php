<?php

namespace Shabayek\Payment\Drivers;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Shabayek\Payment\Contracts\PaymentMethodContract;

/**
 * PaymobMethod class
 * @package Shabayek\Payment\Drivers\PaymobMethod
 */
class PaymobMethod extends Method implements PaymentMethodContract
{
    /**
     * PaymobMethod constructor.
     *
     * @param Array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->setCredentials($config['credentials']);
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
     * Pay with payment method.
     *
     * @param Request $request
     * @return array
     */
    public function pay(Request $request)
    {
        if ($request->isMethod('post')) {
            // The request is using the POST method
            $callback = $this->processesCallback($request->all());
        }
        if ($request->isMethod('get')) {
            // The request is using the GET method
            $callback = $this->responseCallBack($request->all());
        }

        $isSuccess = false;
        try {
            if ($callback['transaction_status']) {
                $downPaymentInfo = [];
                if ($this->isInstallment()) {
                    $orderData = $this->getOrderData($callback['payment_order_id']);

                    $downPaymentInfo = $this->calculateInstallmentFees($orderData);
                }
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
            $response = Http::post("{$this->url}auth/tokens", $postData);

            $result = $response->json();
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
        try {
            $postData = [
                'auth_token' => $token,
                'delivery_needed' => false,
                'merchant_order_id' => $this->transaction_id . "-" . rand(10000, 99999),
                'merchant_id' => $this->merchantID,
                'amount_cents' => (int) $this->amount * 100,
                'currency' => "EGP",
                'items' => $this->getItems(),
                'shipping_data' => [
                    "first_name" => $this->getCustomerDetails('first_name'),
                    "last_name" => $this->getCustomerDetails('last_name'),
                    "email" => $this->getCustomerDetails('email'),
                    "phone_number" => $this->getCustomerDetails('phone'),
                ]
            ];
            $response = Http::post("{$this->url}ecommerce/orders", $postData);

            return $response->json();
        } catch (Exception $e) {
            throw new Exception("Order not created success in paymob #" . $e->getMessage());
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
        try {
            $postData = [
                'auth_token' => $token,
                'amount_cents' => (int) $this->amount * 100,
                'expiration' => 3600,
                'order_id' => $orderPayId,
                'currency' => "EGP",
                'integration_id' => $this->integrationID,
                'billing_data' => [
                    "first_name" => $this->getCustomerDetails('first_name'),
                    "last_name" => $this->getCustomerDetails('last_name'),
                    "email" => $this->getCustomerDetails('email'),
                    "phone_number" => $this->getCustomerDetails('phone'),
                    "apartment" => $this->getAddressDetails('apartment'),
                    "floor" => $this->getAddressDetails('floor'),
                    "city" => $this->getAddressDetails('city'),
                    "state" => $this->getAddressDetails('state'),
                    "street" => $this->getAddressDetails('street'),
                    "building" => $this->getAddressDetails('building'),
                    "country" => "EG",
                ],
            ];
    
            $response = Http::post("{$this->url}acceptance/payment_keys", $postData);

            $result = $response->json();
            return $result['token'];
        } catch (Exception $e) {
            throw new Exception("Payment key request not created success in paymob #" . $e->getMessage());
        }
    }
    /**
     * Record processes callback - POST request
     *
     * @param array $requestData
     * @throws Exception
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

        $transaction_status = $requestData['obj']['success'] ?? false;

        return [
            'payment_order_id' => $requestData['obj']['order']['id'],
            'payment_transaction_id' => $requestData['obj']['id'],
            'transaction_status' => $transaction_status === true
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
            'payment_order_id' => $requestData['order'],
            'payment_transaction_id' => $requestData['id'],
            'transaction_status' => $transaction_status === "true"
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
            $value = Arr::get($requestData, $key);
            if ($value === true) {
                $value = "true";
            } elseif ($value === false) {
                $value = "false";
            }
            $requestValues[] = $value;
        }

        return hash_hmac('sha512', implode('', $requestValues), $this->hmacHash);
    }
    /**
     * Get order detials
     *
     * @param int $id
     * @return void|object
     */
    private function getOrderData($id)
    {
        try {
            $token = $this->getAuthenticationToken();
            $response = Http::withToken($token)->get("{$this->url}acceptance/transactions/{$id}");
            
            $result = $response->json();

            if ($response->ok() && isset($result['success']) && $result['success'] === true) {
                return $result;
            }

            throw new Exception($result['detail'] ?? 'Order not found');
        } catch (Exception $e) {
            throw new Exception("Get order data failed in paymob # " . $e->getMessage());
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
