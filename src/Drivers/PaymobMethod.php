<?php

namespace Shabayek\Payment\Drivers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Shabayek\Payment\Contracts\PaymentMethodContract;

/**
 * PaymobMethod class.
 */
class PaymobMethod extends Method implements PaymentMethodContract
{
    /**
     * Set credentials.
     *
     * @param  array  $credentials
     * @return void
     */
    protected function setCredentials(array $credentials)
    {
        parent::setCredentials($credentials);
        $this->url = 'https://accept.paymobsolutions.com/api/';
    }

    /**
     * Purchase with paymant mwthod and get redirect url.
     *
     * @return string|null
     */
    public function purchase()
    {
        $token = $this->getAuthenticationToken();
        $orderCreation = $this->orderCreation($token);
        if ($orderCreation) {
            $paymentKey = $this->paymentKeyRequest($token, $orderCreation['id']);

            return "{$this->url}acceptance/iframes/{$this->iframe_id}?payment_token={$paymentKey}";
        }

        return null;
    }

    /**
     * Pay with payment method.
     *
     * @param  Request  $request
     * @return array
     */
    public function pay(Request $request): array
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
                $message = 'Success';
            } else {
                $message = 'Transaction did not completed';
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
        }

        return [
            'success' => $isSuccess,
            'message' => $message,
            'data'    => $isSuccess ? $callback : [],
        ];
    }

    /**
     * Verify if payment status from gateway.
     *
     * @param  int  $payment_order_id
     * @return array
     */
    public function verify(int $payment_order_id): array
    {
        $token = $this->getAuthenticationToken();
        $response = $this->retrieveTransactionByOrder($payment_order_id, $token);

        $isSuccess = $response['success'];
        if (isset($response['success']) && $response['success']) {
            $downPaymentInfo = [];
            if ($this->isInstallment()) {
                $downPaymentInfo = $this->calculateInstallmentFees($response);
            }

            $callback = [
                'payment_order_id' => $response['order']['id'],
                'payment_transaction_id' => $response['id'],
                'transaction_status' => $response['success'],
                'down_payment_info' => $downPaymentInfo,
            ];
        }

        return [
            'success' => $isSuccess,
            'message' => 'Verify payment status successfully',
            'data' => $isSuccess ? $callback : [],
        ];
    }

    /**
     * Authentication Request.
     *
     * @return string
     */
    private function getAuthenticationToken()
    {
        $postData = ['api_key' => $this->api_key];

        $response = Http::post("{$this->url}auth/tokens", $postData);
        $result = $response->json();

        if ($response->successful()) {
            return $result['token'];
        }

        $this->setErrors('Authentication failed in paymob Api key not found');
    }

    /**
     * Order registration API.
     *
     * @param  string  $token
     * @return object
     */
    private function orderCreation($token)
    {
        $postData = [
            'auth_token'        => $token,
            'delivery_needed'   => false,
            'merchant_order_id' => $this->transaction_id.'-'.rand(10000, 99999),
            'merchant_id'       => $this->merchant_id,
            'amount_cents'      => $this->amount * 100,
            'currency'          => 'EGP',
            'items'             => $this->getItems(),
            'shipping_data'     => [
                'first_name'   => $this->getCustomerDetails('first_name'),
                'last_name'    => $this->getCustomerDetails('last_name'),
                'email'        => $this->getCustomerDetails('email'),
                'phone_number' => $this->getCustomerDetails('phone'),
            ],
        ];
        $response = Http::post("{$this->url}ecommerce/orders", $postData);
        $result = $response->json();

        if ($response->successful()) {
            return $result;
        }

        $this->setErrors('Order not created success in paymob');
    }

    /**
     * Get payment key request.
     *
     * @param  int  $orderPayId
     * @return string
     */
    private function paymentKeyRequest($token, $orderPayId)
    {
        $postData = [
            'auth_token'     => $token,
            'amount_cents'   => (int) $this->amount * 100,
            'expiration'     => 3600,
            'order_id'       => $orderPayId,
            'currency'       => 'EGP',
            'integration_id' => $this->integration_id,
            'billing_data'   => [
                'first_name'   => $this->getCustomerDetails('first_name'),
                'last_name'    => $this->getCustomerDetails('last_name'),
                'email'        => $this->getCustomerDetails('email'),
                'phone_number' => $this->getCustomerDetails('phone'),
                'apartment'    => $this->getAddressDetails('apartment'),
                'floor'        => $this->getAddressDetails('floor'),
                'city'         => $this->getAddressDetails('city'),
                'state'        => $this->getAddressDetails('state'),
                'street'       => $this->getAddressDetails('street'),
                'building'     => $this->getAddressDetails('building'),
                'country'      => 'EG',
            ],
        ];

        $response = Http::post("{$this->url}acceptance/payment_keys", $postData);
        $result = $response->json();

        if ($response->successful()) {
            return $result['token'];
        }

        $this->setErrors('Payment key request not created success in paymob');
    }

    /**
     * Record processes callback - POST request.
     *
     * @param  array  $requestData
     * @return array
     *
     * @throws Exception
     */
    private function processesCallback($requestData): array
    {
        $hmacKeys = [
            'amount_cents',
            'created_at',
            'currency',
            'error_occured',
            'has_parent_transaction',
            'id',
            'integration_id',
            'is_3d_secure',
            'is_auth',
            'is_capture',
            'is_refunded',
            'is_standalone_payment',
            'is_voided',
            'order.id',
            'owner',
            'pending',
            'source_data.pan',
            'source_data.sub_type',
            'source_data.type',
            'success',
        ];
        $hmac = $this->calculateHmac($requestData, $hmacKeys);
        if ($hmac != $requestData['hmac']) {
            throw new Exception('HMAC is not valid');
        }

        $transaction_status = $requestData['obj']['success'] ?? false;

        return [
            'payment_order_id'       => $requestData['obj']['order']['id'],
            'payment_transaction_id' => $requestData['obj']['id'],
            'transaction_status'     => $transaction_status === true,
        ];
    }

    /**
     * Record response callback - GET request.
     *
     * @param  array  $requestData
     * @return array
     */
    private function responseCallBack($requestData)
    {
        $hmacKeys = [
            'amount_cents',
            'created_at',
            'currency',
            'error_occured',
            'has_parent_transaction',
            'id',
            'integration_id',
            'is_3d_secure',
            'is_auth',
            'is_capture',
            'is_refunded',
            'is_standalone_payment',
            'is_voided',
            'order',
            'owner',
            'pending',
            'source_data_pan',
            'source_data_sub_type',
            'source_data_type',
            'success',
        ];
        $hmac = $this->calculateHmac($requestData, $hmacKeys);
        if ($hmac != $requestData['hmac']) {
            throw new Exception('HMAC is not valid');
        }

        $transaction_status = $requestData['success'];

        return [
            'payment_order_id'       => $requestData['order'],
            'payment_transaction_id' => $requestData['id'],
            'transaction_status'     => $transaction_status === 'true',
        ];
    }

    /**
     * HMAC Calculation.
     *
     * @param  array  $requestData
     * @param  array  $hmacKeys
     * @return string
     */
    private function calculateHmac(array $requestData, array $hmacKeys): string
    {
        $requestValues = [];
        foreach ($hmacKeys as $key) {
            $value = Arr::get($requestData, $key);
            if ($value === true) {
                $value = 'true';
            } elseif ($value === false) {
                $value = 'false';
            }
            $requestValues[] = $value;
        }

        return hash_hmac('sha512', implode('', $requestValues), $this->hmac_hash);
    }

    /**
     * Get order detials.
     *
     * @param  int  $id
     * @return void|object
     */
    private function getOrderData($id)
    {
        $token = $this->getAuthenticationToken();
        $response = Http::withToken($token)->get("{$this->url}acceptance/transactions/{$id}");
        $result = $response->json();

        if ($response->successful() && isset($result['success']) && $result['success'] === true) {
            return $result;
        }

        $this->setErrors($result['detail'] ?? 'Order not found');
        return [];
    }

    /**
     * Retrieve transaction by order from paymob.
     *
     * @param  int  $payment_order_id
     * @param  string  $token
     * @return array
     */
    private function retrieveTransactionByOrder($payment_order_id, $token)
    {
        $host = $this->url.'ecommerce/orders/transaction_inquiry';
        $requestBody = [
            'auth_token' => $token,
            'order_id' => $payment_order_id,
        ];
        $response = Http::post($host, $requestBody);
        if ($response->ok()) {
            return $response->json();
        }
        $this->setErrors($response->json()['detail'] ?? 'Transaction not found');
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
            case 'valu':
                $result['down_payment'] = data_get($orderDetials, 'data.down_payment') ?? 0;
                $result['admin_fees'] = 0; // $orderDetials->data->purchase_fees ?? 0; ##Change happened in valu response
                break;
            case 'shahry':
                $result['down_payment'] = data_get($orderDetials, 'data.shahry_order.down_payment') ?? 0;
                $result['admin_fees'] = data_get($orderDetials, 'data.shahry_order.administrative_fees') ?? 0;
                break;
            case 'souhoola':
                $result['down_payment'] = data_get($orderDetials, 'data.installment_info.downpaymentValue') ?? 0;
                $result['admin_fees'] = data_get($orderDetials, 'data.installment_info.adminFees') ?? 0;
                break;
            case 'get_go':
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

    /**
     * Get items.
     *
     * @return array
     */
    private function getItems()
    {
        if (empty($this->items)) {
            throw new \InvalidArgumentException('Items not set.');
        }

        return collect($this->items)->map(function ($item) {
            return [
                'name' => $item['name'],
                'amount_cents' => $item['price'] * 100,
                'quantity' => $item['quantity'],
                'description' => $item['description'] ?? 'NA',
            ];
        })->toArray();
    }
}
