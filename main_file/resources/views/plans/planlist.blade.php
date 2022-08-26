<div class="row">
    @foreach ($plans as $key => $plan)
        @if($plan->status)
            <div class="col-xs-12 col-sm-12 col-md-{{$size}} col-lg-{{$size}} mt-4">
                <div class="card card-pricing popular text-center px-3 mb-5 mb-lg-0">
                    <span class="h6 w-60 mx-auto px-4 py-1 rounded-bottom bg-primary text-white">{{ $plan->name }}</span>
                    <div class="card-body delimiter-top">
                        <ul class="list-unstyled mb-4">
                            @if($plan->id != 1)
                                @if($plan->trial_days == 0)
                                    <li>{{ __('Trial') }} : {{$plan->trial_days}} {{ __('Days') }}</li>
                                @endif
                                <li>{{ __('Monthly Price') }}: {{(env('CURRENCY') ? env('CURRENCY') : '$')}}{{$plan->monthly_price}}</li>
                                <li>{{ __('Annual Price') }}: {{(env('CURRENCY') ? env('CURRENCY') : '$')}}{{$plan->annual_price}}</li>
                            @endif
                            @if($plan->max_users != 0)
                                <li>{{ ($plan->max_users < 0)?__('Unlimited'):$plan->max_users }} {{__('Users')}}</li>
                            @endif
                            @if($plan->max_projects != 0)
                                <li>{{ ($plan->max_projects < 0)?__('Unlimited'):$plan->max_projects }} {{__('Projects')}}</li>
                            @endif
                            @if($plan->description)
                                <li>
                                    <small>{{$plan->description}}</small>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="card-footer delimiter-top">
                        <div class="row justify-content-center">
                            <?php $planStatus = true; ?>
                            @if(\Auth::user()->type != 'client' && \Auth::user()->plan != '' && \Auth::user()->plan == $plan->id && date('Y-m-d') < \Auth::user()->plan_expire_date && (\Auth::user()->is_plan_purchased == 0 || \Auth::user()->plan_expire_date < date('Y-m-d')))
                                <button class="btn btn-sm btn-neutral mb-3">
                                    <a>
                                        @if(Auth::user()->is_trial_done && Auth::user()->is_plan_purchased == 0)
                                            @if(Auth::user()->plan_expire_date < date('Y-m-d'))
                                                {{__('Trial Expired : ')}}
                                            @else
                                                {{__('Trial Expire on : ')}}
                                            @endif
                                            <?php $planStatus = false; ?>
                                        @elseif(Auth::user()->plan != '' && Auth::user()->plan_expire_date < date('Y-m-d'))
                                            {{__('Plan Expired : ')}}
                                            <?php $planStatus = false; ?>
                                        @endif
                                        @if($planStatus)
                                            {{__('Plan Expire on : ')}}
                                        @endif
                                        {{ (date('d M Y',strtotime(\Auth::user()->plan_expire_date))) }}
                                    </a>
                                </button>
                                @if(Auth::user()->plan != $plan->id)
                                    @if($paymentSetting['enable_stripe'] == 'on' || $paymentSetting['enable_paypal'] == 'on' || $paymentSetting['is_paystack_enabled'] == 'on' || $paymentSetting['is_flutterwave_enabled'] == 'on' || $paymentSetting['is_razorpay_enabled'] == 'on' || $paymentSetting['is_mercado_enabled'] == 'on' || $paymentSetting['is_paytm_enabled'] == 'on' || $paymentSetting['is_mollie_enabled'] == 'on' || $paymentSetting['is_skrill_enabled'] == 'on' || $paymentSetting['is_coingate_enabled'] == 'on' || $paymentSetting['is_paymentwall_enabled'] == 'on')
                                        <div class="col-auto mb-2">
                                            <a href="{{route('payment',\Illuminate\Support\Facades\Crypt::encrypt($plan->id))}}" id="interested_plan_{{ $plan->id }}" class="btn btn-xs btn-primary btn-icon rounded-pill">
                                                <span class="btn-inner--icon"><i class="fas fa-cart-plus"></i></span>
                                                <span class="btn-inner--text">{{ __('Subscribe') }}</span>
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            @elseif(\Auth::user()->plan == $plan->id && date('Y-m-d') < \Auth::user()->plan_expire_date)
                                <button class="btn btn-sm btn-neutral mb-3">
                                    <a>{{__('Expire on ')}} {{ (date('d M Y',strtotime(\Auth::user()->plan_expire_date))) }}</a>
                                </button>
                            @elseif($paymentSetting['enable_stripe'] == 'on' || $paymentSetting['enable_paypal'] == 'on' || $paymentSetting['is_paystack_enabled'] == 'on' || $paymentSetting['is_flutterwave_enabled'] == 'on' || $paymentSetting['is_razorpay_enabled'] == 'on' || $paymentSetting['is_mercado_enabled'] == 'on' || $paymentSetting['is_paytm_enabled'] == 'on' || $paymentSetting['is_mollie_enabled'] == 'on' || $paymentSetting['is_skrill_enabled'] == 'on' || $paymentSetting['is_coingate_enabled'] == 'on'  || $paymentSetting['is_paymentwall_enabled'] == 'on')
                                @if(\Auth::user()->is_trial_done == 0 && $plan->id != 1)
                                    <div class="col-auto mb-2">
                                        <a href="{{route('take.a.plan.trial',$plan->id)}}" id="trial_{{ $plan->id }}" class="btn btn-xs btn-primary btn-icon rounded-pill">
                                            <span class="btn-inner--icon"><i class="fas fa-cart-plus"></i></span>
                                            <span class="btn-inner--text">{{ __('Take a Trial') }}</span>
                                        </a>
                                    </div>
                                @endif
                                @if(\Auth::user()->plan == $plan->id && $plan->id == 1)
                                    <button class="btn btn-sm btn-neutral mb-3">
                                        <a>{{__('Plan Expire on : ').''.__('Unlimited')}}</a>
                                    </button>
                                @else
                                    <div class="col-auto mb-2">
                                        <a href="{{route('payment',\Illuminate\Support\Facades\Crypt::encrypt($plan->id))}}" id="interested_plan_{{ $plan->id }}" class="btn btn-xs btn-primary btn-icon rounded-pill">
                                            <span class="btn-inner--icon"><i class="fas fa-cart-plus"></i></span>
                                            <span class="btn-inner--text">{{ __('Subscribe') }}</span>
                                        </a>
                                    </div>
                                    @if($plan->id != 1)
                                        <div class="col-auto mb-2">
                                            @if(\Auth::user()->requested_plan != $plan->id)
                                                <a href="#" class="btn btn-xs btn-primary btn-icon rounded-pill" data-url="{{route('request.view',\Illuminate\Support\Facades\Crypt::encrypt($plan->id))}}" data-ajax-popup="true" data-title="{{__('Manually Request')}}">
                                                    <span class="btn-inner--icon"><i class="fas fa-share"></i></span>
                                                    <span class="btn-inner--text">{{ __('Manually Request') }}</span>
                                                </a>
                                            @else
                                                <a href="{{ route('request.cancel',\Auth::user()->id) }}" class="btn btn-xs btn-primary btn-icon rounded-pill">
                                                    <span class="btn-inner--icon"><i class="fas fa-times"></i></span>
                                                    <span class="btn-inner--text">{{ __('Cancel Request') }}</span>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>
