<?php

namespace Shabayek\Payment\Drivers;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Shabayek\Payment\Contracts\PaymentMethodContract;
use Shabayek\Payment\Enums\Gateway;

/**
 * MastercardMethod class.
 */
class MastercardMethod extends AbstractMethod implements PaymentMethodContract
{
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

                $transaction->update([
                    'session_id'        => $session_id,
                    'success_indicator' => $result['successIndicator'],
                ]);

                return view('payment::mastercard', [
                    'merchant_id'    => $this->merchant_id,
                    'checkout_js'    => $this->checkout_js,
                    'total_amount'   => $this->amount,
                    'customer_id'    => $this->customer->id,
                    'transaction_id' => $this->transaction_id,
                    'callback_url'   => $this->callback_url,
                    'session_id'     => $session_id,
                ]);
            } else {
                throw new Exception(json_encode($result));
            }
        } catch (Exception $e) {
            $this->setErrors('create session in mastercard failed # '.$e->getMessage());
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
