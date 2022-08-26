@component('mail::message')
<p>{{__('Hello,')}}</p>
<br>
<p>{{__($invoice->name .' Shared you this invoice')}}</p>
<br>

@component('mail::button', ['url' => $invoice->url])
{{__('Click to Download Invoice')}}
@endcomponent

{{__('Thanks,')}}<br>
{{ config('app.name') }}
@endcomponent
