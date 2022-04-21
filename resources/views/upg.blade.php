<!DOCTYPE html>
<html lang="en">

<head>

    <title>Laravel Payment Package | UPG</title>

    <script src="{{ $lightbox_js }}"></script>
    <script type="text/javascript">
        function callLightbox() {
            Lightbox.Checkout.configure = {
                OrderId: "",
                paymentMethodFromLightBox: null,
                MID: "{{ $mID }}",
                TID: "{{ $tID }}",
                SecureHash: "{{ $secureHash }}",
                TrxDateTime: "{{ $trxDateTime }}",
                Currency: "{{ config('payment.currency') }}",
                AmountTrxn: "{{ $amount }}",
                MerchantReference: "{{ $order_id }}",
                ReturnUrl: "{{ $returnUrl }}",
                completeCallback: function(data) {
                    //your code here
                },
                errorCallback: function(e) {
                    //your code here
                    console.log(e);
                },
                cancelCallback: function() {
                    //your code here
                }
            };
            Lightbox.Checkout.showPaymentPage();
        }

        callLightbox();
    </script>

</head>

<body>

</body>

</html>
