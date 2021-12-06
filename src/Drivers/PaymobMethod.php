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
    public function pay()
    {
        // code here
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
}
