<div class="row">
    <div class="col-md-12 col-12">
        <div class="alert alert-warning invite-warning" style="display: none;"></div>
    </div>
    <div class="col-md-12 col-12 add_user_div d-none">
        <div class="form-group">
            {{ Form::label('username', __('Name'),['class' => 'form-control-label']) }}
            {{ Form::text('username', null, ['class' => 'form-control', 'placeholder' => __('Please enter name')]) }}
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            {{ Form::label('add_email', __('Email'),['class' => 'form-control-label']) }}
            {{ Form::email('add_email', null, ['class' => 'form-control', 'placeholder' => __('Enter email address')]) }}
        </div>
    </div>
    <div class="col-md-12 col-12 add_user_div d-none">
        <div class="form-group">
            {{ Form::label('userpassword', __('Password'),['class' => 'form-control-label']) }}
            {{ Form::text('userpassword', null, ['class' => 'form-control', 'placeholder' => __('Please enter password')]) }}
        </div>
    </div>
    <div class="col-md-12 col-12">
        <label class="form-control-label">{{__('Role')}}</label> <br>
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <label class="btn btn-primary btn-sm active">
                <input type="radio" name="user_type" id="radio_user" autocomplete="off" value="user" checked>{{__('User')}}
            </label>
            <label class="btn btn-primary btn-sm">
                <input type="radio" name="user_type" id="radio_client" autocomplete="off" value="client">{{__('Client')}}
            </label>
        </div>
    </div>
</div>

<div class="text-right">
    {{ Form::button(__('Add'), ['type' => 'button','class' => 'btn btn-sm btn-primary rounded-pill check-add-user']) }}
</div>
