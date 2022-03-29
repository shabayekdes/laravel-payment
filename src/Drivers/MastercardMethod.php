<?php

namespace Shabayek\Payment\Drivers;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Shabayek\Payment\Contracts\PaymentMethodContract;

/**
 * MastercardMethod class.
 */
class MastercardMethod extends AbstractMethod implements PaymentMethodContract
{
    private const STATUS_APPROVED = 'APPROVED';
    private const RESPONSE_SUCCESS = 'SUCCESS';

    /**
     * Purchase with payment method and get redirect url.
     *
     * @return string|null
     */
    public function purchase()
    {
        //
    }

    /**
     * Payment checkout view.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $transaction
     * @return void
     */
    public function checkoutForm(Model $transaction)
    {
        try {
            $postRequest = [
                'apiOperation' => 'CREATE_CHECKOUT_SESSION',
                'order' => [
                    'id' => $this->transaction_id,
                    'amount' => $this->amount,
                    'currency' => config('payment.currency'),
                ],
                'interaction' => [
                    'operation' => 'PURCHASE',
                ],
                'airline' => [
                    'ticket' => [
                        'ticketNumber' => $this->ticketNumber ?? '',
                    ],
                ],
            ];

            $response = Http::withBasicAuth($this->username, $this->password)
                ->post($this->baseUrl."/merchant/{$this->merchant_id}/session", $postRequest);

            $result = $response->json();

            if (isset($result['result']) && $result['result'] == self::RESPONSE_SUCCESS) {
                $session_id = $result['session']['id'];

                $transaction->update([
                    'session_id' => $session_id,
                    'success_indicator'=> $result['successIndicator'],
                ]);

                return view('payment::mastercard', [
                    'merchant_id' => $this->merchant_id,
                    'checkout_js' => $this->checkout_js,
                    'total_amount' => $this->amount,
                    'customer_id' => $this->customer->id,
                    'session_id' => $session_id,
                ]);
            } else {
                Log::error("Error in creating session for transaction {$transaction->id} # ".json_encode($result));
                throw new Exception('BANK INSTALLMENT ERROR');
            }
        } catch (Exception $e) {
            $this->setErrors('Order not created session in mastercard');
        }
    }

    /**
     * Pay with payment method.
     *
     * @param  Request  $request
     * @return array
     */
    public function pay(Request $request): array
    {
        // write logic here

        return [
            'success' => true,
            'message' => 'success',
            'data'    => [],
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
        // write logic here

        return [
            'success' => true,
            'message' => 'Verify payment status successfully',
            'data' => [],
        ];
    }
}
