@component('mail::message')
<p>{{__('Hello, '. $invoice->name)}}</p>
<br>
@component('mail::button', ['url' => $invoice->url])
{{__('Click to Download Invoice')}}
@endcomponent

{{__('Thanks,')}}<br>
{{ config('app.name') }}
@endcomponent
