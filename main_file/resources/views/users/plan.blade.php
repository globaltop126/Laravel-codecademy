<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <tbody>
                @foreach($plans as $plan)
                    <tr>
                        <td>
                            <div class="font-style font-weight-bold">{{$plan->name}}</div>
                        </td>
                        <td>
                            <div class="font-weight-bold">{{$plan->max_users}}</div>
                            <div>{{__('Users')}}</div>
                        </td>
                        <td>
                            <div class="font-weight-bold">{{$plan->max_projects}}</div>
                            <div>{{__('Projects')}}</div>
                        </td>
                        <td>
                            @if($user->plan == $plan->id)
                                <button type="button" class="btn btn-sm btn-soft-success btn-icon rounded-pill">
                                    <span class="btn-inner--icon"><i class="fas fa-check"></i></span>
                                    <span class="btn-inner--text">{{__('Active')}}</span>
                                </button>
                            @else
                                @if($plan->id == 1)
                                    <a href="{{route('plan.active',[$user->id,$plan->id, 'duration' => 'monthly'])}}" class="btn btn-primary btn-xs" title="Click to Upgrade Plan"><i class="fas fa-cart-plus"></i> {{ __('Active') }}</a>
                                @else
                                    <div>
                                        <a href="{{route('plan.active',[$user->id,$plan->id, 'duration' => 'monthly'])}}" class="btn btn-primary btn-xs" title="Click to Upgrade Plan"><i class="fas fa-cart-plus"></i> {{ __('One Month') }}</a>
                                        <a href="{{route('plan.active',[$user->id,$plan->id, 'duration' => 'annual'])}}" class="btn btn-primary btn-xs" title="Click to Upgrade Plan"><i class="fas fa-cart-plus"></i> {{ __('One Year') }}</a>
                                    </div>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
