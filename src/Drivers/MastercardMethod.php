<?php

namespace Shabayek\Payment\Drivers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Shabayek\Payment\Contracts\CheckoutFormContract;
use Shabayek\Payment\Contracts\PaymentMethodContract;
use Shabayek\Payment\Enums\Gateway;

/**
 * MastercardMethod class.
 *
 * @author Esmail Shabayek <esmail.shabayek@gmail.com>
 */
class MastercardMethod extends AbstractMethod implements CheckoutFormContract
{
    private $base_url;
    private $username;
    private $password;
    private $merchant_id;
    private $checkout_js;
    private $callback_url;

    /**
     * Payment checkout view.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|null
     */
    public function checkoutForm()
    {
        try {
            $postRequest = [
                'apiOperation' => 'CREATE_CHECKOUT_SESSION',
                'order'        => [
                    'id'       => $this->transaction_id,
                    'amount'   => $this->amount,
                    'currency' => config('payment.currency'),
                ],
                'interaction'   => [
                    'operation' => 'PURCHASE',
                ],
            ];
            if (isset($this->ticket_number)) {
                $postRequest['airline']['ticket']['ticketNumber'] = $this->ticket_number;
            }

            $response = Http::withBasicAuth($this->username, $this->password)
                ->post($this->base_url."/merchant/{$this->merchant_id}/session", $postRequest);

            $result = $response->json();

            if (isset($result['result']) && $result['result'] == Gateway::MASTERCARD_RESPONSE_SUCCESS) {
                $session_id = $result['session']['id'];

                $this->payment_reference = $result['successIndicator'];

                return view('payment::mastercard', [
                    'merchant_id'    => $this->merchant_id,
                    'checkout_js'    => $this->checkout_js,
                    'total_amount'   => $this->amount,
                    'customer_id'    => $this->customer->id,
                    'transaction_id' => $this->transaction_id,
                    'callback_url'   => $this->callback_url,
                    'session_id'     => $session_id,
                ]);
            }

            throw new Exception(json_encode($result));
        } catch (Exception $e) {
            $this->setErrors('create session in mastercard failed # '.$e->getMessage());
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
        $isSuccess = false;

        if ($request->has('result') && $request->get('result') == Gateway::MASTERCARD_RESPONSE_SUCCESS) {
            $isSuccess = true;

            $callback = [
                'resultIndicator' => $request->get('resultIndicator'),
            ];
        }

        return [
            'success' => $isSuccess,
            'message' => 'success',
            'data'    => $isSuccess ? $callback : [],
        ];
    }

    /**
     * Verify if payment status from gateway.
     *
     * @param int $payment_order_id
     * @return array
     *
     * @throws Exception
     */
    public function verify(int $payment_order_id): array
    {
        // write logic here
        throw new \Exception('Not implement');

        return [];
    }
}
