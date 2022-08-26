@php
  $invoice = $data['amount'];
  $invoice_id = $data['invoice_id'];
@endphp

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{\App\Models\Utility::invoiceNumberFormat($invoice_id)}}  &dash; {{(Utility::getValByName('header_text')) ? Utility::getValByName('header_text') : config('app.name') }}</title>
    <link rel="icon" href="{{ asset(Storage::url('logo/favicon.png')) }}" type="image/png">
</head>



{{-- {{ dd( $admin_payment_setting) }} --}}
<script src="https://api.paymentwall.com/brick/build/brick-default.1.5.0.min.js"> </script>
<div id="payment-form-container"> </div>
<script>
  var brick = new Brick({
    public_key: '{{ $company_payment_setting['paymentwall_public_key'] }}', // please update it to Brick live key before launch your project
    amount: '{{ $invoice }}',
    currency: 'USD',
    container: 'payment-form-container',
    action: '{{route("invoice.pay.with.paymentwall",[$data["invoice_id"],"amount" => $data["amount"]])}}',
    form: {
      merchant: 'Paymentwall',
      product: '{{\App\Models\Utility::invoiceNumberFormat($invoice_id)}}',
      pay_button: 'Pay',
      show_zip: true, // show zip code 
      show_cardholder: true // show card holder name 
    }
});

brick.showPaymentForm(function(data) {
      if(data.flag == 1){
        console.log('dsfrserf');
        window.location.href ='{{route("error.invoice.show",[1, 'invoice_id'])}}'.replace('invoice_id',data.invoice);
      }else{
        console.log('22222');
        window.location.href ='{{route("error.invoice.show",[2, 'invoice_id'])}}'.replace('invoice_id',data.invoice);
      }
    }, function(errors) {
      if(errors.flag == 1){
        console.log('xcfdr');
        window.location.href ='{{route("error.invoice.show",[1,'invoice_id'])}}'.replace('invoice_id',errors.invoice);
      }else{
        console.log('11111');
        window.location.href ='{{route("error.invoice.show",[2, 'invoice_id'])}}'.replace('invoice_id',errors.invoice);
      }
      	   
    });
  
</script>