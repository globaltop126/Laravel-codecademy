<form class="pl-3 pr-3" method="post" action="{{ route('test.email.send') }}" id="test_email">
    @csrf
    <div class="form-group">
        <label for="email" class="form-control-label">{{ __('E-Mail Address') }}</label>
        <input type="email" class="form-control" id="email" name="email" required/>
    </div>
    <div class="form-group">
        <input type="hidden" name="mail_driver" value="{{$data['mail_driver']}}"/>
        <input type="hidden" name="mail_host" value="{{$data['mail_host']}}"/>
        <input type="hidden" name="mail_port" value="{{$data['mail_port']}}"/>
        <input type="hidden" name="mail_username" value="{{$data['mail_username']}}"/>
        <input type="hidden" name="mail_password" value="{{$data['mail_password']}}"/>
        <input type="hidden" name="mail_encryption" value="{{$data['mail_encryption']}}"/>
        <input type="hidden" name="mail_from_address" value="{{$data['mail_from_address']}}"/>
        <input type="hidden" name="mail_from_name" value="{{$data['mail_from_name']}}"/>
        <div class="row">
            <div class="col-6">
                <button class="btn btn-sm btn-primary rounded-pill" type="submit">{{ __('Send Test Mail') }}</button>
            </div>
            <div class="col-6 text-right pt-2">
                <label id="email_sanding" style="display: none"><i class="fas fa-clock"></i> {{__('Sending ...')}} </label>
            </div>
        </div>
    </div>
</form>
