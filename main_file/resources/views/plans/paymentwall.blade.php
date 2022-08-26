@php
    $plan_id= \Illuminate\Support\Facades\Crypt::decrypt($data['plan_id']);
    $plan = App\Models\Plan::find($plan_id);
    $plan_name = $plan->name;
    if($data['paymentwall_payment_frequency']=="monthly")
    {
      $plandata=App\Models\Plan::where('id',$plan_id)->first();
      $planprice=$plandata->monthly_price;
    }
    else
    {
       $plandata=App\Models\Plan::where('id',$plan_id)->first();
       $planprice=$plandata->annual_price;
    }
 @endphp

<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Plan PaymentWall &dash; {{(Utility::getValByName('header_text')) ? Utility::getValByName('header_text') : config('app.name') }}</title>
  <link rel="icon" href="{{ asset(Storage::url('logo/favicon.png')) }}" type="image/png">
</head>



{{-- {{ dd( $admin_payment_setting) }} --}}
<script src="https://api.paymentwall.com/brick/build/brick-default.1.5.0.min.js"> </script>
<div id="payment-form-container"> </div>
<script>
var brick = new Brick({
  public_key: '{{ $admin_payment_setting['paymentwall_public_key'] }}', // please update it to Brick live key before launch your project
  amount: '{{ $planprice }}' ,
  currency: 'USD',
  container: 'payment-form-container',
  action: '{{route("plan.pay.with.paymentwall",[$data["plan_id"],$data["coupon"],"amount" => $data['paymentwall_payment_frequency']])}}',
  form: {
    merchant: 'Paymentwall',
    product: '{{ $plan_name }}',
    pay_button: 'Pay',
    show_zip: true, // show zip code 
    show_cardholder: true // show card holder name 
  }
});

brick.showPaymentForm(function(data) {
    if(data.flag == 1){
      window.location.href ='{{route("error.plan.show",1)}}';
    }else{
      window.location.href ='{{route("error.plan.show",2)}}';
    }
  }, function(errors) {
    if(errors.flag == 1){
      window.location.href ='{{route("error.plan.show",1)}}';
    }else{
      window.location.href ='{{route("error.plan.show",2)}}';
    }
         
  });

</script>