<ul class="nav nav-tabs nav-overflow" role="tablist">
    <li class="nav-item">
        <a href="#role_users" id="user-tab" class="user_role nav-link active" data-val="user" data-toggle="tab" role="tab" aria-controls="home" aria-selected="true">
            {{__('Users')}}
        </a>
    </li>
    <li class="nav-item">
        <a href="#role_clients" id="client-tab" class="user_role nav-link" data-val="client" data-toggle="tab" role="tab" aria-controls="home" aria-selected="true">
            {{__('Clients')}}
        </a>
    </li>
</ul>
<div class="row">
    <div class="col-md-12 col-12 pt-3">
        <div class="alert alert-warning invite-warning" style="display: none;"></div>
    </div>
    <div class="col-md-12 col-12 invite_user_div d-none">
        <div class="form-group">
            {{ Form::label('username', __('Name'),['class' => 'form-control-label']) }}
            {{ Form::text('username', null, ['class' => 'form-control', 'placeholder' => __('Please enter name')]) }}
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            {{ Form::hidden('project_id', $project_id) }}
            {{ Form::label('invite_email', __('Email'),['class' => 'form-control-label']) }}
            {{ Form::email('invite_email', null, ['class' => 'form-control', 'placeholder' => __('Enter email address')]) }}
            <input type="hidden" id="usr_role" name="usr_role" value="user"/>
        </div>
    </div>
    <div class="col-md-12 col-12 invite_user_div d-none">
        <div class="form-group">
            {{ Form::label('userpassword', __('Password'),['class' => 'form-control-label']) }}
            {{ Form::text('userpassword', null, ['class' => 'form-control', 'placeholder' => __('Please enter password')]) }}
        </div>
    </div>
</div>
<div class="text-right">
    {{ Form::button(__('Invite'), ['type' => 'button','class' => 'btn btn-sm btn-primary rounded-pill check-invite-members']) }}
</div>

<div class="tab-content">
    <div class="tab-pane fade show active" id="role_users" role="tabpanel" aria-labelledby="user-tab">
        @if(count($users) > 0)
            <div class="invite_usr">
                <hr class="mb-2">
                <div class="row pb-1">
                    <div class="col-12 text-center">
                        <label class="form-control-label">{{__('Invite User From Your Contact')}}</label>
                    </div>
                    @foreach($users as $usr)
                        <div class="col-6 col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="avatar-parent-child">
                                        <a href="#" class="avatar avatar-lg rounded-circle">
                                            <img {{ $usr->img_avatar }}/>
                                        </a>
                                    </div>
                                    <h6 class="text-sm my-3">{{ $usr->name }}</h6>
                                    <button type="button" class="btn btn-xs btn-secondary invite-btn" data-id="{{ $usr->id }}">{{__('Invite')}}</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    <div class="tab-pane fade show" id="role_clients" role="tabpanel" aria-labelledby="client-tab">
        @if(count($clients) > 0)
            <div class="invite_usr">
                <hr class="mb-2">
                <div class="row pb-1">
                    <div class="col-12 text-center">
                        <label class="form-control-label">{{__('Invite Client From Your Contact')}}</label>
                    </div>
                    @foreach($clients as $client)
                        <div class="col-6 col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="avatar-parent-child">
                                        <a href="#" class="avatar avatar-lg rounded-circle">
                                            <img {{ $client->img_avatar }}/>
                                        </a>
                                    </div>
                                    <h6 class="text-sm my-3">{{ $client->name }}</h6>
                                    <button type="button" class="btn btn-xs btn-secondary invite-btn" data-id="{{ $client->id }}">{{__('Invite')}}</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
