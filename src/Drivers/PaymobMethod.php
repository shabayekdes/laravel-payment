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
        $this->client = new Client();
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
     * @return void
     */
    private function getAuthenticationToken()
    {
        try {
            $response = $this->client->request('POST', "{$this->url}auth/tokens", [
                'api_key' => $this->authApiKey
            ]);

            $result = $response->json();
            return $result['token'];
        } catch (\Exception $e) {
            return false;
        }
    }
}
