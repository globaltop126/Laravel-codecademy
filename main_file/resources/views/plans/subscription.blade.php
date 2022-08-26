@extends('layouts.admin')

@section('title')
    {{__('Choose Plan')}}
@endsection

@section('content')
    @include('plans.planlist',['size'=>'4','paymentSetting'=>\App\Models\Utility::getPaymentSetting()])
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            var tohref = '';

            @if(\Auth::user()->is_register_trial == 1)
            var tohref = $('#trial_{{ Auth::user()->interested_plan_id }}').attr("href");
            @elseif(\Auth::user()->interested_plan_id != 0)
            var tohref = $('#interested_plan_{{ Auth::user()->interested_plan_id }}').attr("href");
            @endif

            if (tohref != '') {
                window.location = tohref;
            }
        });
    </script>
@endpush
