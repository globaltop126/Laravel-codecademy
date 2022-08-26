@if(isset($users) && !empty($users) && count($users) > 0)
    @foreach($users as $user)
        <div class="col-lg-3 col-sm-6">
            <div class="card hover-shadow-lg">
                <div class="card-body text-center">
                    <div class="avatar-parent-child">
                        <img {{ $user->img_avatar }} class="avatar rounded-circle avatar-lg">
                    </div>
                    <h5 class="h6 mt-4 mb-0">
                        <p class="mb-1">{{ $user->name }}</p>
                    </h5>
                    <p class="d-block text-sm text-muted mb-1">{{ $user->email }}</p>
                    <small data-toggle="tooltip" data-placement="bottom" data-original-title="{{__('Last Login')}}">{{ (!empty($user->last_login_at)) ? Utility::getDateFormated($user->last_login_at,true) : '-' }}</small>
                </div>
                <div class="card-body border-top">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-6 text-center">
                            <span class="d-block font-weight-bold mb-0">{{ $user->getPlan() ? $user->getPlan()->name : '-' }}</span>
                        </div>
                        <div class="col-6 text-center">
                            <a href="#" class="btn rounded btn-xs btn-primary" data-url="{{ route('plan.upgrade',$user->id) }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Upgrade Plan')}}">{{__('Upgrade')}}</a>
                        </div>
                        <div class="col-12">
                            <hr class="my-3">
                        </div>
                        <div class="col-6 text-center">
                            <span class="d-block h4 mb-0">{{ count($user->contacts) }}</span>
                            <span class="d-block text-sm text-muted">{{__('Members')}}</span>
                        </div>
                        <div class="col-6 text-center">
                            <span class="d-block h4 mb-0">{{ $user->projects->count() }}</span>
                            <span class="d-block text-sm text-muted">{{__('Projects')}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="col-xl-12 col-lg-12 col-sm-12">
        <div class="card">
            <div class="card-body">
                <h6 class="text-center mb-0">{{__('No User Found.')}}</h6>
            </div>
        </div>
    </div>
@endif
