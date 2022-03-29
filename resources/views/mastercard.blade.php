<!DOCTYPE html>
<html class="" lang="en">
<head>

    <title>Laravel Payment Package | MasterCard</title>

    <script src="{{ $checkout_js }}" data-error="errorCallback" data-cancel="cancelCallback"
        data-complete="{{ $callback_url }}">
    </script>

    <script type="text/javascript">
        function errorCallback(error) {
            console.log(JSON.stringify(error));
        }

        function cancelCallback() {
            console.log('Payment cancelled');
        }


        Checkout.configure({
            merchant: "{{ $merchant_id }}",
            order: {
                amount: "{{ $total_amount }}",
                currency: "{{ config('payment.currency') }}",
                description: 'Customer Id #{{ $customer_id }}',
                id: "{{ $transaction_id }}",
            },
            interaction: {
                operation: "PURCHASE",
                merchant: {
                    name: 'Online Payment',
                },
                displayControl: {
                    billingAddress: 'HIDE',
                    customerEmail: 'HIDE',
                    orderSummary: 'HIDE',
                    shipping: 'HIDE'
                },
            },
            session: {
                id: "{{ $session_id }}"
            }
        });
        // Checkout.showPaymentPage();
        Checkout.showLightbox();
    </script>

</head>

<body>

</body>

</html>
