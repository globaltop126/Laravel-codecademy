<div class="card card-pricing popular text-center px-3 mb-5 mb-lg-0">
    <span class="h6 w-60 mx-auto px-4 py-1 rounded-bottom bg-primary text-white">{{ $plan->name }}</span>
    <div class="card-body delimiter-top">
        <ul class="list-unstyled mb-4">
            <li>{{ __('Monthly Price') }}: {{(env('CURRENCY') ? env('CURRENCY') : '$')}}{{$plan->monthly_price}}</li>
            <li>{{ __('Annual Price') }}: {{(env('CURRENCY') ? env('CURRENCY') : '$')}}{{$plan->annual_price}}</li>
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
            <div class="col-auto mb-2">
                <a href="{{ route('send.request',[\Illuminate\Support\Facades\Crypt::encrypt($plan->id),'monthly']) }}" class="btn btn-xs btn-primary btn-icon rounded-pill">
                    <span class="btn-inner--icon"><i class="fas fa-share"></i></span>
                    <span class="btn-inner--text">{{__('Monthly Update Request')}}</span>
                </a>
            </div>
            <div class="col-auto mb-2">
                <a href="{{ route('send.request',[\Illuminate\Support\Facades\Crypt::encrypt($plan->id),'annual']) }}" class="btn btn-xs btn-primary btn-icon rounded-pill">
                    <span class="btn-inner--icon"><i class="fas fa-share"></i></span>
                    <span class="btn-inner--text">{{__('Annually Update Request')}}</span>
                </a>
            </div>
        </div>
    </div>
</div>
