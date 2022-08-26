@component('mail::message')
<p>{{__('Hello, '. $invoice->name)}}</p>
<br>
<span>{{__('It remind to you, amount ')}}<b>{{$invoice->getDue}}</b> {{__('is pending for invoice ')}} <b>{{$invoice->invoice}}</b></span>
<br>
@component('mail::button', ['url' => $invoice->url])
{{__('Click to Download Invoice')}}
@endcomponent

{{__('Thanks,')}}<br>
{{ config('app.name') }}
@endcomponent
