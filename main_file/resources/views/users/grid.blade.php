@if(isset($users) && !empty($users) && count($users) > 0)
    @foreach($users as $user)
        @php($commonData = $user->usrCommonData())
        <div class="col-lg-3 col-sm-6">
            <div class="card hover-shadow-lg">
                <div class="card-header border-0 pb-0 pt-2 px-3">
                    <div class="text-right">
                        <span class="badge badge-xs badge-{{$user->usrRole()['color']}}">{{__($user->usrRole()['role'])}}</span>
                    </div>
                </div>
                <div class="card-body text-center">
                    <div class="avatar-parent-child">
                        <img {{ $user->img_avatar }} class="avatar rounded-circle avatar-lg">
                    </div>
                    <h5 class="h6 mt-4 mb-0">
                        @if($user->is_active == 1)
                            <a href="{{ route('users.info',$user->id) }}">{{ $user->name }}</a>
                        @else
                            <a href="#">{{ $user->name }}</a>
                        @endif
                    </h5>
                    <p class="d-block text-sm text-muted mb-3">{{ $user->email }}</p>
                    @if($user->is_active == 1)
                        <div class="actions px-4 pb-2">
                            <a href="{{ route('users.info',$user->id) }}" class="action-item mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="#" class="action-item mr-3" data-size="lg" data-url="{{route('user.reset',\Crypt::encrypt($user->id))}}" data-ajax-popup="true" data-title="{{__('Forgot Password')}}" data-toggle="tooltip" data-original-title="{{__('Forgot Password')}}">
                                <i class="fas fa-key"></i>
                            </a>
                            <a href="#" class="action-item" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?')}}|{{__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-user-{{$user->id}}').submit();">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                            {!! Form::open(['method' => 'DELETE', 'route' => ['user.destroy',$user->id],'id'=>'delete-user-'.$user->id]) !!}
                            {!! Form::close() !!}
                        </div>
                    @else
                        <div class="actions px-4 pb-2">
                            <a href="#" class="action-item" data-toggle="tooltip" data-original-title="{{__('Locked')}}">
                                <i class="fas fa-lock"></i>
                            </a>
                        </div>
                    @endif
                    <small data-toggle="tooltip" data-placement="bottom" data-original-title="{{__('Last Login')}}">{{ (!empty($user->last_login_at)) ? Utility::getDateFormated($user->last_login_at,true) : '-' }}</small>
                </div>
                <div class="card-body border-top">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-6">
                            <div class="max-w-120">
                                <div class="spark-chart" data-toggle="spark-chart" data-type="line" data-height="50" data-color="success" data-dataset="{{ json_encode(array_values($commonData['timesheet']))}}"></div>
                            </div>
                        </div>
                        <div class="col-auto text-center">
                            <span class="d-block h4 mb-0">{{ $commonData['open_task'] }}</span>
                            <span class="d-block text-sm text-muted">{{__('Open tasks')}}</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="actions d-flex justify-content-between">
                        <a href="#" data-url="{{ route('user.info.popup',[$user->id,'project']) }}" data-ajax-popup="true" data-size="md" data-title="{{ $user->name.__("'s Projects")}}" class="action-item">
                            <span class="btn-inner--icon">{{__('Projects')}}</span>
                        </a>
                        <a href="#" data-url="{{ route('user.info.popup',[$user->id,'due_task']) }}" data-ajax-popup="true" data-size="md" data-title="{{ $user->name.__("'s Top Due Tasks")}}" class="action-item">
                            <span class="btn-inner--icon">{{__('Due Tasks')}}</span>
                        </a>
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
