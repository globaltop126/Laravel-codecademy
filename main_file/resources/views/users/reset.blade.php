{{Form::model($user,array('route' => array('user.password.update', $user->user_id), 'method' => 'post')) }}
<div class="row">
    <div class="form-group col-md-6">
        {{ Form::label('password', __('Password')) }}
       <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
       @error('password')
       <span class="invalid-feedback" role="alert">
               <strong>{{ $message }}</strong>
           </span>
       @enderror
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('password_confirmation', __('Confirm Password')) }}
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
    </div>
</div>
<div class="modal-footer">
    {{Form::submit(__('Upadte'),array('class'=>'btn btn-sm btn-primary rounded-pill'))}}
</div>

{{ Form::close() }}
