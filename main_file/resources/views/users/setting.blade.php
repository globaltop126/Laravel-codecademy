@extends('layouts.admin')

@section('title')
    {{ __('Site Settings') }}
@endsection

@php

if ($settings['color']) {
    $color = $settings['color'];
}

@endphp

@push('css')
    <style>
        .doc-img>a,
        .theme-color>a {
            position: relative;
            width: 35px;
            height: 25px;
            border-radius: 3px;
            display: inline-block;
            background: #f8f9fd;
            overflow: hidden;
            box-shadow: 0 1px 2px rgb(0 0 0 / 28%);
        }
    </style>
@endpush


@section('content')
    <div class="row">
        <div class="col-lg-4 order-lg-2">
            <div class="card">
                <div class="list-group list-group-flush" id="tabs">
                    <div data-href="#tabs-1" class="list-group-item text-primary">
                        <div class="media">
                            <i class="fas fa-cog pt-1"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{ __('Site Setting') }}</a>
                                <p class="mb-0 text-sm">{{ __('Details about your personal information') }}</p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#tabs-2" class="list-group-item">
                        <div class="media">
                            <i class="fas fa-envelope pt-1"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{ __('Mailer Settings') }}</a>
                                <p class="mb-0 text-sm">{{ __('Details about your mail settingsetting information') }}</p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#tabs-4" class="list-group-item">
                        <div class="media">
                            <i class="fas fa-comments pt-1"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{ __('Pusher Settings') }}</a>
                                <p class="mb-0 text-sm">{{ __('Details about your pusher setting information for chat') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#tabs-3" class="list-group-item">
                        <div class="media">
                            <i class="fas fa-money-check-alt pt-1"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{ __('Payment Settings') }}</a>
                                <p class="mb-0 text-sm">{{ __('Details about your Payment setting information') }}</p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#tabs-5" class="list-group-item">
                        <div class="media">
                            <i class="fas fa-money-check-alt pt-1"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{ __('ReCaptcha Settings') }}</a>
                                <p class="mb-0 text-sm">
                                    {{ __('Test to tell human and bots apart by adding Recaptcha setting') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 order-lg-1">

            <div id="tabs-1" class="tabs-card">
                <div class="card">
                    <div class="card-header">
                        <h5 class="h6 mb-0">{{ __('Basic Setting') }}</h5>
                    </div>
                    <div class="card-body">
                        {{ Form::open(['route' => ['settings.store'], 'id' => 'update_setting', 'enctype' => 'multipart/form-data']) }}
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('full_logo', __('Logo'), ['class' => 'form-control-label']) }}
                                    <input type="file" name="full_logo" id="full_logo" class="custom-input-file"
                                        onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])" />
                                    <label for="full_logo">
                                        <i class="fa fa-upload"></i>
                                        <span>{{ __('Choose a file…') }}</span>
                                    </label>
                                    @error('full_logo')
                                        <span class="full_logo" role="alert">
                                            <small class="text-danger">{{ $message }}</small>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 pt-5">
                                <a href="{{ asset(Storage::url('logo/logo.png')) }}" target="_blank">
                                    <img src="{{ asset(Storage::url('logo/logo.png')) }}" id="blah"
                                        class="img_setting" />
                                </a>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('favicon', __('Favicon'), ['class' => 'form-control-label']) }}
                                    <input type="file" name="favicon" id="favicon" class="custom-input-file"
                                        onchange="document.getElementById('blah1').src = window.URL.createObjectURL(this.files[0])" />
                                    <label for="favicon">
                                        <i class="fa fa-upload"></i>
                                        <span>{{ __('Choose a file…') }}</span>
                                    </label>
                                    @error('favicon')
                                        <span class="favicon" role="alert">
                                            <small class="text-danger">{{ $message }}</small>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 pt-5">
                                <a href="{{ asset(Storage::url('logo/favicon.png')) }}" target="_blank">
                                    <img src="{{ asset(Storage::url('logo/favicon.png')) }}" id="blah1"
                                        class="img_setting" />
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('header_text', __('Title Text'), ['class' => 'form-control-label']) }}
                                    {{ Form::text('header_text', \App\Models\Utility::getValByName('header_text'), ['class' => 'form-control', 'placeholder' => __('Enter Header Title Text')]) }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('footer_text', __('Footer Text'), ['class' => 'form-control-label']) }}
                                    {{ Form::text('footer_text', \App\Models\Utility::getValByName('footer_text'), ['class' => 'form-control', 'placeholder' => __('Enter Footer Text')]) }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('default_language', __('Default Language'), ['class' => 'form-control-label text-dark']) }}
                                    <select name="default_language" id="default_language" class="form-control select2">
                                        @foreach (Utility::languages() as $language)
                                            <option @if (Utility::getValByName('default_language') == $language) selected @endif
                                                value="{{ $language }}">{{ Str::upper($language) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('timezone', __('Timezone'), ['class' => 'form-control-label text-dark']) }}
                                    <select name="timezone" id="timezone" class="form-control select2">
                                        @foreach ($timezones as $k => $timezone)
                                            <option value="{{ $k }}"
                                                {{ env('TIMEZONE') == $k ? 'selected' : '' }}>{{ $timezone }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 mt-lg-5">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="enable_landing"
                                        id="enable_landing"
                                        {{ \App\Models\Utility::getValByName('enable_landing') == 'on' ? 'checked' : '' }}>
                                    <label class="custom-control-label form-control-label"
                                        for="enable_landing">{{ __('Enable Landing Page') }}</label>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 mt-lg-5">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="enable_rtl"
                                        id="enable_rtl"
                                        {{ \App\Models\Utility::getValByName('enable_rtl') == 'on' ? 'checked' : '' }}>
                                    <label class="custom-control-label form-control-label"
                                        for="enable_rtl">{{ __('Enable RTL') }}</label>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 mt-lg-5">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input gdpr_fulltime gdpr_type"
                                        name="gdpr_cookie" id="gdpr_cookie"
                                        {{ isset($settings['gdpr_cookie']) && $settings['gdpr_cookie'] == 'on' ? 'checked="checked"' : '' }}>
                                    <label class="custom-control-label form-control-label"
                                        for="gdpr_cookie">{{ __('GDPR Cookie') }}</label>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 mt-lg-5">
                                <div class="custom-control custom-switch">

                                    <input type="checkbox" class="custom-control-input" name="SIGNUP" id="SIGNUP"
                                        {{ isset($settings['SIGNUP']) && $settings['SIGNUP'] == 'on' ? 'checked="checked"' : '' }}>
                                    <label class="custom-control-label form-control-label"
                                        for="SIGNUP">{{ __('SIGNUP') }}</label>
                                </div>
                            </div>


                            <div class="form-group col-lg-12">
                                {{ Form::label('cookie_text', __('GDPR Cookie Text'), ['class' => 'fulltime']) }}
                                {!! Form::textarea('cookie_text', $settings['cookie_text'], ['class' => 'form-control fulltime', 'rows' => '4']) !!}

                            </div>

                            <div class="col-4">
                                <h6 class="">
                                    <i data-feather="credit-card" class="me-2"></i>{{ __('Primary color settings') }}
                                </h6>
                                <hr class="my-2" />
                                <div class="theme-color themes-color">

                                 
                                        <a href="#!" class="theme-2 {{($color =='#6fd943') ? 'active_color' : ''}}" data-value="#6fd943" onclick="check_theme('#6fd943')"></a>
                                        <input type="radio" class="theme_color " name="color" value="#6fd943" style="display: none;">
                                        
                                        <a href="#!" class="theme-1 {{($color =='#a83f85') ? 'active_color' : ''}}" data-value="#a83f85" onclick="check_theme('#a83f85')"></a>
                                        <input type="radio" class="theme_color " name="color" value="#a83f85" style="display: none;">
                                        
                                        <a href="#!" class="theme-3 {{($color =='theme-3') ? 'active_color' : ''}}" data-value="theme-3" onclick="check_theme('#449fc6')"></a>
                                        <input type="radio" class="theme_color " name="color" value="#449fc6" style="display: none;">

                                        <a href="#!" class="theme-4 {{($color =='#51459d') ? 'active_color' : ''}}" data-value="theme-4" onclick="check_theme('#51459d')"></a>
                                        <input type="radio" class="theme_color " name="color" value="#51459d" style="display: none;">

                                </div>
                            </div>


                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('footer_link_1', __('Footer Link Title 1'), ['class' => 'form-control-label']) }}
                                    {{ Form::text('footer_link_1', \App\Models\Utility::getValByName('footer_link_1'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Footer Link Title 1')]) }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('footer_value_1', __('Footer Link href 1'), ['class' => 'form-control-label']) }}
                                    {{ Form::text('footer_value_1', \App\Models\Utility::getValByName('footer_value_1'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Footer Link 1')]) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('footer_link_2', __('Footer Link Title 2'), ['class' => 'form-control-label']) }}
                                    {{ Form::text('footer_link_2', \App\Models\Utility::getValByName('footer_link_2'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Footer Link Title 2')]) }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('footer_value_2', __('Footer Link href 2'), ['class' => 'form-control-label']) }}
                                    {{ Form::text('footer_value_2', \App\Models\Utility::getValByName('footer_value_2'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Footer Link 2')]) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('footer_link_3', __('Footer Link Title 3'), ['class' => 'form-control-label']) }}
                                    {{ Form::text('footer_link_3', \App\Models\Utility::getValByName('footer_link_3'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Footer Link Title 3')]) }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('footer_value_3', __('Footer Link href 3'), ['class' => 'form-control-label']) }}
                                    {{ Form::text('footer_value_3', \App\Models\Utility::getValByName('footer_value_3'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Footer Link 3')]) }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            {{ Form::hidden('from', 'site_setting') }}
                            <button type="submit"
                                class="btn btn-sm btn-primary rounded-pill">{{ __('Save changes') }}</button>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>       


            <div id="tabs-2" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <h5 class="h6 mb-0">{{ __('Mailer Settings') }}</h5>
                    </div>
                    <div class="card-body">
                        {{ Form::open(['route' => ['settings.store'], 'id' => 'update_setting']) }}
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('mail_driver', __('Mail Driver'), ['class' => 'form-control-label']) }}
                                    {{ Form::text('mail_driver', env('MAIL_DRIVER'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Mail Driver')]) }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('mail_host', __('Mail Host'), ['class' => 'form-control-label']) }}
                                    {{ Form::text('mail_host', env('MAIL_HOST'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Mail Host')]) }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('mail_port', __('Mail Port'), ['class' => 'form-control-label']) }}
                                    {{ Form::number('mail_port', env('MAIL_PORT'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Mail Port'), 'min' => '0']) }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('mail_username', __('Mail Username'), ['class' => 'form-control-label']) }}
                                    {{ Form::text('mail_username', env('MAIL_USERNAME'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Mail Username')]) }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('mail_password', __('Mail Password'), ['class' => 'form-control-label']) }}
                                    {{ Form::text('mail_password', env('MAIL_PASSWORD'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Mail Password')]) }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('mail_encryption', __('Mail Encryption'), ['class' => 'form-control-label']) }}
                                    {{ Form::text('mail_encryption', env('MAIL_ENCRYPTION'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Mail Encryption')]) }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('mail_from_address', __('Mail From Address'), ['class' => 'form-control-label']) }}
                                    {{ Form::text('mail_from_address', env('MAIL_FROM_ADDRESS'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Mail From Address')]) }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('mail_from_name', __('Mail From Name'), ['class' => 'form-control-label']) }}
                                    {{ Form::text('mail_from_name', env('MAIL_FROM_NAME'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Mail From Name')]) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="text-left">
                                    <button type="button" class="btn btn-sm btn-warning rounded-pill send_email"
                                        data-title="{{ __('Send Test Mail') }}"
                                        data-url="{{ route('test.email') }}">{{ __('Send Test Mail') }}</button>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="text-right">
                                    {{ Form::hidden('from', 'mail') }}
                                    <button type="submit"
                                        class="btn btn-sm btn-primary rounded-pill">{{ __('Save changes') }}</button>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
            <div id="tabs-4" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <h5 class="h6 mb-0">{{ __('Pusher Settings') }}</h5>
                    </div>
                    <div class="card-body">
                        {{ Form::open(['route' => ['settings.store'], 'id' => 'update_setting']) }}
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('pusher_app_id', __('Pusher App Id'), ['class' => 'form-control-label']) }}
                                    {{ Form::text('pusher_app_id', env('PUSHER_APP_ID'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Pusher App Id')]) }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('pusher_app_key', __('Pusher App Key'), ['class' => 'form-control-label']) }}
                                    {{ Form::text('pusher_app_key', env('PUSHER_APP_KEY'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Pusher App Key')]) }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('pusher_app_secret', __('Pusher App Secret'), ['class' => 'form-control-label']) }}
                                    {{ Form::text('pusher_app_secret', env('PUSHER_APP_SECRET'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Pusher App Secret')]) }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('pusher_app_cluster', __('Pusher App Cluster'), ['class' => 'form-control-label']) }}
                                    {{ Form::text('pusher_app_cluster', env('PUSHER_APP_CLUSTER'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Pusher App Cluster')]) }}
                                </div>
                            </div>
                            <div class="col-12">
                                <small><a href="https://pusher.com/channels"
                                        target="_blank">{{ __('You can Make Pusher channel Account from here and Get your App Id and Secret key') }}</a></small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="text-right">
                                    {{ Form::hidden('from', 'pusher') }}
                                    <button type="submit"
                                        class="btn btn-sm btn-primary rounded-pill">{{ __('Save changes') }}</button>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
            <div id="tabs-3" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <h5 class="h6 mb-0">{{ __('Payment Settings') }}</h5>
                        <small>{{ __('This detail will use for collect payment on plan from company . On plan company will find out pay now button based on your below configuration.') }}</small>
                    </div>
                    <div class="card-body">
                        {{ Form::open(['route' => ['settings.store'], 'id' => 'update_setting']) }}
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('currency', __('Currency'), ['class' => 'form-control-label']) }}
                                    {{ Form::text('currency', env('CURRENCY'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Currency')]) }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    {{ Form::label('currency_code', __('Currency Code'), ['class' => 'form-control-label']) }}
                                    {{ Form::text('currency_code', env('CURRENCY_CODE'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Currency Code')]) }}
                                    <small>{{ __('Note : Add currency code as per three-letter ISO code.') }} <a
                                            href="https://stripe.com/docs/currencies"
                                            target="_blank">{{ __('you can find out here..') }}</a></small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div id="payment-gateways" class="accordion accordion-spaced">
                                    <!-- Stripe -->
                                    <div class="card">
                                        <div class="card-header py-4" id="stripe-payment" data-toggle="collapse"
                                            role="button" data-target="#collapse-stripe" aria-expanded="false"
                                            aria-controls="collapse-stripe">
                                            <h6 class="mb-0"><i
                                                    class="far fa-credit-card mr-3"></i>{{ __('Stripe') }}</h6>
                                        </div>
                                        <div id="collapse-stripe" class="collapse" aria-labelledby="stripe-payment"
                                            data-parent="#payment-gateways">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-6 py-2">
                                                        <h5 class="h5">{{ __('Stripe') }}</h5>
                                                        <small>{{ __('Note: This detail will use for make checkout of plan.') }}</small>
                                                    </div>
                                                    <div class="col-6 py-2 text-right">
                                                        <div class="custom-control custom-switch float-right">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="enable_stripe" id="enable_stripe"
                                                                {{ isset($payment_detail['enable_stripe']) && $payment_detail['enable_stripe'] == 'on' ? 'checked' : '' }}>
                                                            <label class="custom-control-label form-control-label"
                                                                for="enable_stripe">{{ __('Enable Stripe') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                        <div class="form-group">
                                                            {{ Form::label('stripe_key', __('Stripe Key'), ['class' => 'form-control-label']) }}
                                                            {{ Form::text('stripe_key', isset($payment_detail['stripe_key']) && !empty($payment_detail['stripe_key']) ? $payment_detail['stripe_key'] : '', ['class' => 'form-control', 'placeholder' => __('Stripe Key')]) }}
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                        <div class="form-group">
                                                            {{ Form::label('stripe_secret', __('Stripe Secret'), ['class' => 'form-control-label']) }}
                                                            {{ Form::text('stripe_secret', isset($payment_detail['stripe_secret']) && !empty($payment_detail['stripe_secret']) ? $payment_detail['stripe_secret'] : '', ['class' => 'form-control', 'placeholder' => __('Stripe Secret')]) }}
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                        <div class="form-group">
                                                            {{ Form::label('stripe_webhook_secret', __('Stripe Webhook Secret'), ['class' => 'form-control-label']) }}
                                                            {{ Form::text('stripe_webhook_secret', isset($payment_detail['stripe_webhook_secret']) && !empty($payment_detail['stripe_webhook_secret']) ? $payment_detail['stripe_webhook_secret'] : '', ['class' => 'form-control', 'placeholder' => __('Stripe Webhook Secret')]) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paypal -->
                                    <div class="card">
                                        <div class="card-header py-4" id="paypal-payment" data-toggle="collapse"
                                            role="button" data-target="#collapse-paypal" aria-expanded="false"
                                            aria-controls="collapse-paypal">
                                            <h6 class="mb-0"><i
                                                    class="far fa-credit-card mr-3"></i>{{ __('Paypal') }}</h6>
                                        </div>
                                        <div id="collapse-paypal" class="collapse" aria-labelledby="paypal-payment"
                                            data-parent="#payment-gateways">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-6 py-2">
                                                        <h5 class="h5">{{ __('PayPal') }}</h5>
                                                        <small>{{ __('Note: This detail will use for make checkout of plan.') }}</small>
                                                    </div>
                                                    <div class="col-6 py-2 text-right">
                                                        <div class="custom-control custom-switch float-right">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="enable_paypal" id="enable_paypal"
                                                                {{ isset($payment_detail['enable_paypal']) && $payment_detail['enable_paypal'] == 'on' ? 'checked' : '' }}>
                                                            <label class="custom-control-label form-control-label"
                                                                for="enable_paypal">{{ __('Enable Paypal') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                        <label class="paypal-label form-control-label"
                                                            for="paypal_mode">{{ __('Paypal Mode') }}</label> <br>
                                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                            <label
                                                                class="btn btn-primary btn-sm {{ !isset($payment_detail['paypal_mode']) || empty($payment_detail['paypal_mode']) || $payment_detail['paypal_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                <input type="radio" name="paypal_mode" value="sandbox"
                                                                    {{ !isset($payment_detail['paypal_mode']) || empty($payment_detail['paypal_mode']) || $payment_detail['paypal_mode'] == 'sandbox' ? 'checked' : '' }}>{{ __('Sandbox') }}
                                                            </label>
                                                            <label
                                                                class="btn btn-primary btn-sm {{ isset($payment_detail['paypal_mode']) && $payment_detail['paypal_mode'] == 'live' ? 'active' : '' }}">
                                                                <input type="radio" name="paypal_mode" value="live"
                                                                    {{ isset($payment_detail['paypal_mode']) && $payment_detail['paypal_mode'] == 'live' ? 'checked' : '' }}>{{ __('Live') }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                        <div class="form-group">
                                                            {{ Form::label('paypal_client_id', __('Client ID'), ['class' => 'form-control-label']) }}
                                                            {{ Form::text('paypal_client_id', isset($payment_detail['paypal_client_id']) ? $payment_detail['paypal_client_id'] : '', ['class' => 'form-control', 'placeholder' => __('Client ID')]) }}
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                        <div class="form-group">
                                                            {{ Form::label('paypal_secret_key', __('Secret Key'), ['class' => 'form-control-label']) }}
                                                            {{ Form::text('paypal_secret_key', isset($payment_detail['paypal_secret_key']) ? $payment_detail['paypal_secret_key'] : '', ['class' => 'form-control', 'placeholder' => __('Secret Key')]) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paystack -->
                                    <div class="card">
                                        <div class="card-header py-4" id="paystack-payment" data-toggle="collapse"
                                            role="button" data-target="#collapse-paystack" aria-expanded="false"
                                            aria-controls="collapse-paystack">
                                            <h6 class="mb-0"><i
                                                    class="far fa-credit-card mr-3"></i>{{ __('Paystack') }}</h6>
                                        </div>
                                        <div id="collapse-paystack" class="collapse" aria-labelledby="paystack-payment"
                                            data-parent="#payment-gateways">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-6 py-2">
                                                        <h5 class="h5">{{ __('Paystack') }}</h5>
                                                        <small>
                                                            {{ __('Note: This detail will use for make checkout of plan.') }}</small>
                                                    </div>
                                                    <div class="col-6 py-2 text-right">
                                                        <div class="custom-control custom-switch float-right">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="is_paystack_enabled" id="is_paystack_enabled"
                                                                {{ isset($payment_detail['is_paystack_enabled']) && $payment_detail['is_paystack_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="custom-control-label form-control-label"
                                                                for="is_paystack_enabled">{{ __('Enable Paystack') }}</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-control-label"
                                                                for="paypal_client_id">{{ __('Public Key') }}</label>
                                                            <input type="text" name="paystack_public_key"
                                                                id="paystack_public_key" class="form-control"
                                                                value="{{ isset($payment_detail['paystack_public_key']) ? $payment_detail['paystack_public_key'] : '' }}"
                                                                placeholder="{{ __('Public Key') }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-control-label"
                                                                for="paystack_secret_key">{{ __('Secret Key') }}</label>
                                                            <input type="text" name="paystack_secret_key"
                                                                id="paystack_secret_key" class="form-control"
                                                                value="{{ isset($payment_detail['paystack_secret_key']) ? $payment_detail['paystack_secret_key'] : '' }}"
                                                                placeholder="{{ __('Secret Key') }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- FLUTTERWAVE -->
                                    <div class="card">
                                        <div class="card-header py-4" id="flutterwave-payment" data-toggle="collapse"
                                            role="button" data-target="#collapse-flutterwave" aria-expanded="false"
                                            aria-controls="collapse-flutterwave">
                                            <h6 class="mb-0"><i
                                                    class="far fa-credit-card mr-3"></i>{{ __('Flutterwave') }}</h6>
                                        </div>
                                        <div id="collapse-flutterwave" class="collapse"
                                            aria-labelledby="flutterwave-payment" data-parent="#payment-gateways">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-6 py-2">
                                                        <h5 class="h5">{{ __('Flutterwave') }}</h5>
                                                        <small>
                                                            {{ __('Note: This detail will use for make checkout of plan.') }}</small>
                                                    </div>
                                                    <div class="col-6 py-2 text-right">
                                                        <div class="custom-control custom-switch float-right">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="is_flutterwave_enabled" id="is_flutterwave_enabled"
                                                                {{ isset($payment_detail['is_flutterwave_enabled']) && $payment_detail['is_flutterwave_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="custom-control-label form-control-label"
                                                                for="is_flutterwave_enabled">{{ __('Enable Flutterwave') }}</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-control-label"
                                                                for="paypal_client_id">{{ __('Public Key') }}</label>
                                                            <input type="text" name="flutterwave_public_key"
                                                                id="flutterwave_public_key" class="form-control"
                                                                value="{{ isset($payment_detail['flutterwave_public_key']) ? $payment_detail['flutterwave_public_key'] : '' }}"
                                                                placeholder="{{ __('Public Key') }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-control-label"
                                                                for="paystack_secret_key">{{ __('Secret Key') }}</label>
                                                            <input type="text" name="flutterwave_secret_key"
                                                                id="flutterwave_secret_key" class="form-control"
                                                                value="{{ isset($payment_detail['flutterwave_secret_key']) ? $payment_detail['flutterwave_secret_key'] : '' }}"
                                                                placeholder="{{ __('Secret Key') }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Razorpay -->
                                    <div class="card">
                                        <div class="card-header py-4" id="razorpay-payment" data-toggle="collapse"
                                            role="button" data-target="#collapse-razorpay" aria-expanded="false"
                                            aria-controls="collapse-razorpay">
                                            <h6 class="mb-0"><i
                                                    class="far fa-credit-card mr-3"></i>{{ __('Razorpay') }}</h6>
                                        </div>
                                        <div id="collapse-razorpay" class="collapse" aria-labelledby="razorpay-payment"
                                            data-parent="#payment-gateways">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-6 py-2">
                                                        <h5 class="h5">{{ __('Razorpay') }}</h5>
                                                        <small>
                                                            {{ __('Note: This detail will use for make checkout of plan.') }}</small>
                                                    </div>
                                                    <div class="col-6 py-2 text-right">
                                                        <div class="custom-control custom-switch float-right">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="is_razorpay_enabled" id="is_razorpay_enabled"
                                                                {{ isset($payment_detail['is_razorpay_enabled']) && $payment_detail['is_razorpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="custom-control-label form-control-label"
                                                                for="is_razorpay_enabled">{{ __('Enable Razorpay') }}</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-control-label"
                                                                for="paypal_client_id">{{ __('Public Key') }}</label>
                                                            <input type="text" name="razorpay_public_key"
                                                                id="razorpay_public_key" class="form-control"
                                                                value="{{ isset($payment_detail['razorpay_public_key']) ? $payment_detail['razorpay_public_key'] : '' }}"
                                                                placeholder="{{ __('Public Key') }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-control-label"
                                                                for="paystack_secret_key">{{ __('Secret Key') }}</label>
                                                            <input type="text" name="razorpay_secret_key"
                                                                id="razorpay_secret_key" class="form-control"
                                                                value="{{ isset($payment_detail['razorpay_secret_key']) ? $payment_detail['razorpay_secret_key'] : '' }}"
                                                                placeholder="{{ __('Secret Key') }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mercado Pago-->
                                    {{-- <div class="card">
                                        <div class="card-header py-4" id="mercado_pago-payment" data-toggle="collapse" role="button" data-target="#collapse-mercado_pago" aria-expanded="false" aria-controls="collapse-mercado_pago">
                                            <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{__('Mercado Pago')}}</h6>
                                        </div>
                                        <div id="collapse-mercado_pago" class="collapse" aria-labelledby="mercado_pago-payment" data-parent="#payment-gateways">
                                              <div class="card-body">
                                                <div class="row">
                                                    <div class="col-6 py-2">
                                                        <h5 class="h5">{{__('Mercado Pago')}}</h5>
                                                        <small> {{__('Note: This detail will use for make checkout of plan.')}}</small>
                                                    </div>
                                                    <div class="col-6 py-2 text-right">
                                                        <div class="custom-control custom-switch">
                                                            <input type="hidden" name="is_mercado_enabled" value="off">
                                                            <input type="checkbox" class="custom-control-input" name="is_mercado_enabled" id="is_mercado_enabled" {{isset($payment_detail['is_mercado_enabled']) &&  $payment_detail['is_mercado_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="custom-control-label form-control-label" for="is_mercado_enabled">{{__('Enable Mercado Pago')}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 pb-4">
                                                        <label class="coingate-label form-control-label" for="mercado_mode">{{__('Mercado Mode')}}</label> <br>
                                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                            <label class="btn btn-primary btn-sm {{isset($payment_detail['mercado_mode']) && $payment_detail['mercado_mode'] == 'sandbox' ? 'active' : ''}}">
                                                                <input type="radio" name="mercado_mode" value="sandbox" {{ isset($payment_detail['mercado_mode']) && $payment_detail['mercado_mode'] == '' || isset($payment_detail['mercado_mode']) && $payment_detail['mercado_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>{{__('Sandbox')}}
                                                            </label>
                                                            <label class="btn btn-primary btn-sm {{isset($payment_detail['mercado_mode']) && $payment_detail['mercado_mode'] == 'live' ? 'active' : ''}}">
                                                                <input type="radio" name="mercado_mode" value="live" {{ isset($payment_detail['mercado_mode']) && $payment_detail['mercado_mode'] == 'live' ? 'checked="checked"' : '' }}>{{__('Live')}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="mercado_access_token">{{ __('Access Token') }}</label>
                                                            <input type="text" name="mercado_access_token" id="mercado_access_token" class="form-control" value="{{isset($payment_detail['mercado_access_token']) ? $payment_detail['mercado_access_token']:''}}" placeholder="{{ __('Access Token') }}"/>                                                        
                                                            @if ($errors->has('mercado_secret_key'))
                                                                <span class="invalid-feedback d-block">
                                                                    {{ $errors->first('mercado_access_token') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}

                                    {{-- <div class="card">
                                        <div class="card-header py-4" id="mercado_pago-payment" data-toggle="collapse" role="button" data-target="#collapse-mercado_pago" aria-expanded="false" aria-controls="collapse-mercado_pago">
                                            <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{__('Mercado Pago')}}</h6>
                                        </div>
                                        <div id="collapse-mercado_pago" class="collapse" aria-labelledby="mercado_pago-payment" data-parent="#payment-gateways">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-6 py-2">
                                                        <h5 class="h5">{{__('Mercado Pago')}}</h5>
                                                        <small> {{__('Note: This detail will use for make checkout of plan.')}}</small>
                                                    </div>
                                                    <div class="col-6 py-2 text-right">
                                                        <div class="custom-control custom-switch">
                                                            <input type="hidden" name="is_mercado_enabled" value="off">
                                                            <input type="checkbox" class="custom-control-input" name="is_mercado_enabled" id="is_mercado_enabled" {{isset($payment_detail['is_mercado_enabled']) &&  $payment_detail['is_mercado_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="custom-control-label form-control-label" for="is_mercado_enabled">{{__('Enable Mercado Pago')}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 pb-4">
                                                        <label class="coingate-label form-control-label" for="mercado_mode">{{__('Mercado Mode')}}</label> <br>
                                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                            <label class="btn btn-primary btn-sm {{isset($payment_detail['mercado_mode']) && $payment_detail['mercado_mode'] == 'sandbox' ? 'active' : ''}}">
                                                                <input type="radio" name="mercado_mode" value="sandbox" {{ isset($payment_detail['mercado_mode']) && $payment_detail['mercado_mode'] == '' || isset($payment_detail['mercado_mode']) && $payment_detail['mercado_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>{{__('Sandbox')}}
                                                            </label>
                                                            <label class="btn btn-primary btn-sm {{isset($payment_detail['mercado_mode']) && $payment_detail['mercado_mode'] == 'live' ? 'active' : ''}}">
                                                                <input type="radio" name="mercado_mode" value="live" {{ isset($payment_detail['mercado_mode']) && $payment_detail['mercado_mode'] == 'live' ? 'checked="checked"' : '' }}>{{__('Live')}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="mercado_access_token">{{ __('Access Token') }}</label>
                                                            <input type="text" name="mercado_access_token" id="mercado_access_token" class="form-control" value="{{isset($payment_detail['mercado_access_token']) ? $payment_detail['mercado_access_token']:''}}" placeholder="{{ __('Access Token') }}"/>                                                        
                                                            @if ($errors->has('mercado_secret_key'))
                                                                <span class="invalid-feedback d-block">
                                                                    {{ $errors->first('mercado_access_token') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}

                                    <!-- Paytm -->
                                    <div class="card">
                                        <div class="card-header py-4" id="paytm-payment" data-toggle="collapse"
                                            role="button" data-target="#collapse-paytm" aria-expanded="false"
                                            aria-controls="collapse-paytm">
                                            <h6 class="mb-0"><i
                                                    class="far fa-credit-card mr-3"></i>{{ __('Paytm') }}</h6>
                                        </div>
                                        <div id="collapse-paytm" class="collapse" aria-labelledby="paytm-payment"
                                            data-parent="#payment-gateways">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-6 py-2">
                                                        <h5 class="h5">{{ __('Paytm') }}</h5>
                                                        <small>
                                                            {{ __('Note: This detail will use for make checkout of plan.') }}</small>
                                                    </div>
                                                    <div class="col-6 py-2 text-right">
                                                        <div class="custom-control custom-switch float-right">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="is_paytm_enabled" id="is_paytm_enabled"
                                                                {{ isset($payment_detail['is_paytm_enabled']) && $payment_detail['is_paytm_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="custom-control-label form-control-label"
                                                                for="is_paytm_enabled">{{ __('Enable Paytm') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 pb-4">
                                                        <label class="paypal-label form-control-label"
                                                            for="paypal_mode">{{ __('Paytm Environment') }}</label> <br>
                                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                            <label
                                                                class="btn btn-primary btn-sm {{ isset($payment_detail['paytm_mode']) && $payment_detail['paytm_mode'] == 'local' ? 'active' : '' }}">
                                                                <input type="radio" name="paytm_mode" value="local"
                                                                    {{ (isset($payment_detail['paytm_mode']) && $payment_detail['paytm_mode'] == '') || (isset($payment_detail['paytm_mode']) && $payment_detail['paytm_mode'] == 'local') ? 'checked="checked"' : '' }}>{{ __('Local') }}
                                                            </label>
                                                            <label
                                                                class="btn btn-primary btn-sm {{ isset($payment_detail['paytm_mode']) && $payment_detail['paytm_mode'] == 'live' ? 'active' : '' }}">
                                                                <input type="radio" name="paytm_mode"
                                                                    value="production"
                                                                    {{ isset($payment_detail['paytm_mode']) && $payment_detail['paytm_mode'] == 'production' ? 'checked="checked"' : '' }}>{{ __('Production') }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-control-label"
                                                                for="paytm_public_key">{{ __('Merchant ID') }}</label>
                                                            <input type="text" name="paytm_merchant_id"
                                                                id="paytm_merchant_id" class="form-control"
                                                                value="{{ isset($payment_detail['paytm_merchant_id']) ? $payment_detail['paytm_merchant_id'] : '' }}"
                                                                placeholder="{{ __('Merchant ID') }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-control-label"
                                                                for="paytm_secret_key">{{ __('Merchant Key') }}</label>
                                                            <input type="text" name="paytm_merchant_key"
                                                                id="paytm_merchant_key" class="form-control"
                                                                value="{{ isset($payment_detail['paytm_merchant_key']) ? $payment_detail['paytm_merchant_key'] : '' }}"
                                                                placeholder="{{ __('Merchant Key') }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-control-label"
                                                                for="paytm_industry_type">{{ __('Industry Type') }}</label>
                                                            <input type="text" name="paytm_industry_type"
                                                                id="paytm_industry_type" class="form-control"
                                                                value="{{ isset($payment_detail['paytm_industry_type']) ? $payment_detail['paytm_industry_type'] : '' }}"
                                                                placeholder="{{ __('Industry Type') }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mollie -->
                                    <div class="card">
                                        <div class="card-header py-4" id="mollie-payment" data-toggle="collapse"
                                            role="button" data-target="#collapse-mollie" aria-expanded="false"
                                            aria-controls="collapse-mollie">
                                            <h6 class="mb-0"><i
                                                    class="far fa-credit-card mr-3"></i>{{ __('Mollie') }}</h6>
                                        </div>
                                        <div id="collapse-mollie" class="collapse" aria-labelledby="mollie-payment"
                                            data-parent="#payment-gateways">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-6 py-2">
                                                        <h5 class="h5">{{ __('Mollie') }}</h5>
                                                        <small>
                                                            {{ __('Note: This detail will use for make checkout of plan.') }}</small>
                                                    </div>
                                                    <div class="col-6 py-2 text-right">
                                                        <div class="custom-control custom-switch float-right">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="is_mollie_enabled" id="is_mollie_enabled"
                                                                {{ isset($payment_detail['is_mollie_enabled']) && $payment_detail['is_mollie_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="custom-control-label form-control-label"
                                                                for="is_mollie_enabled">{{ __('Enable Mollie') }}</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-control-label"
                                                                for="mollie_api_key">{{ __('Mollie Api Key') }}</label>
                                                            <input type="text" name="mollie_api_key"
                                                                id="mollie_api_key" class="form-control"
                                                                value="{{ isset($payment_detail['mollie_api_key']) ? $payment_detail['mollie_api_key'] : '' }}"
                                                                placeholder="{{ __('Mollie Api Key') }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-control-label"
                                                                for="mollie_profile_id">{{ __('Mollie Profile Id') }}</label>
                                                            <input type="text" name="mollie_profile_id"
                                                                id="mollie_profile_id" class="form-control"
                                                                value="{{ isset($payment_detail['mollie_profile_id']) ? $payment_detail['mollie_profile_id'] : '' }}"
                                                                placeholder="{{ __('Mollie Profile Id') }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-control-label"
                                                                for="mollie_partner_id">{{ __('Mollie Partner Id') }}</label>
                                                            <input type="text" name="mollie_partner_id"
                                                                id="mollie_partner_id" class="form-control"
                                                                value="{{ isset($payment_detail['mollie_partner_id']) ? $payment_detail['mollie_partner_id'] : '' }}"
                                                                placeholder="{{ __('Mollie Partner Id') }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Skrill -->
                                    <div class="card">
                                        <div class="card-header py-4" id="skrill-payment" data-toggle="collapse"
                                            role="button" data-target="#collapse-skrill" aria-expanded="false"
                                            aria-controls="collapse-skrill">
                                            <h6 class="mb-0"><i
                                                    class="far fa-credit-card mr-3"></i>{{ __('Skrill') }}</h6>
                                        </div>
                                        <div id="collapse-skrill" class="collapse" aria-labelledby="skrill-payment"
                                            data-parent="#payment-gateways">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-6 py-2">
                                                        <h5 class="h5">{{ __('Skrill') }}</h5>
                                                        <small>
                                                            {{ __('Note: This detail will use for make checkout of plan.') }}</small>
                                                    </div>
                                                    <div class="col-6 py-2 text-right">
                                                        <div class="custom-control custom-switch float-right">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="is_skrill_enabled" id="is_skrill_enabled"
                                                                {{ isset($payment_detail['is_skrill_enabled']) && $payment_detail['is_skrill_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="custom-control-label form-control-label"
                                                                for="is_skrill_enabled">{{ __('Enable Skrill') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-control-label"
                                                                for="mollie_api_key">{{ __('Skrill Email') }}</label>
                                                            <input type="email" name="skrill_email" id="skrill_email"
                                                                class="form-control"
                                                                value="{{ isset($payment_detail['skrill_email']) ? $payment_detail['skrill_email'] : '' }}"
                                                                placeholder="{{ __('Skrill Email') }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CoinGate -->
                                    <div class="card">
                                        <div class="card-header py-4" id="coingate-payment" data-toggle="collapse"
                                            role="button" data-target="#collapse-coingate" aria-expanded="false"
                                            aria-controls="collapse-coingate">
                                            <h6 class="mb-0"><i
                                                    class="far fa-credit-card mr-3"></i>{{ __('CoinGate') }}</h6>
                                        </div>
                                        <div id="collapse-coingate" class="collapse" aria-labelledby="coingate-payment"
                                            data-parent="#payment-gateways">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-6 py-2">
                                                        <h5 class="h5">{{ __('CoinGate') }}</h5>
                                                        <small>
                                                            {{ __('Note: This detail will use for make checkout of plan.') }}</small>
                                                    </div>
                                                    <div class="col-6 py-2 text-right">
                                                        <div class="custom-control custom-switch float-right">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="is_coingate_enabled" id="is_coingate_enabled"
                                                                {{ isset($payment_detail['is_coingate_enabled']) && $payment_detail['is_coingate_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="custom-control-label form-control-label"
                                                                for="is_coingate_enabled">{{ __('Enable CoinGate') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 pb-4">
                                                        <label class="coingate-label form-control-label"
                                                            for="coingate_mode">{{ __('CoinGate Mode') }}</label> <br>
                                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                            <label
                                                                class="btn btn-primary btn-sm {{ isset($payment_detail['coingate_mode']) && $payment_detail['coingate_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                <input type="radio" name="coingate_mode"
                                                                    value="sandbox"
                                                                    {{ (isset($payment_detail['coingate_mode']) && $payment_detail['coingate_mode'] == '') || (isset($payment_detail['coingate_mode']) && $payment_detail['coingate_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>{{ __('Sandbox') }}
                                                            </label>
                                                            <label
                                                                class="btn btn-primary btn-sm {{ isset($payment_detail['coingate_mode']) && $payment_detail['coingate_mode'] == 'live' ? 'active' : '' }}">
                                                                <input type="radio" name="coingate_mode" value="live"
                                                                    {{ isset($payment_detail['coingate_mode']) && $payment_detail['coingate_mode'] == 'live' ? 'checked="checked"' : '' }}>{{ __('Live') }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-control-label"
                                                                for="coingate_auth_token">{{ __('CoinGate Auth Token') }}</label>
                                                            <input type="text" name="coingate_auth_token"
                                                                id="coingate_auth_token" class="form-control"
                                                                value="{{ isset($payment_detail['coingate_auth_token']) ? $payment_detail['coingate_auth_token'] : '' }}"
                                                                placeholder="{{ __('CoinGate Auth Token') }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paymentwall -->
                                    <div class="card">
                                        <div class="card-header py-4" id="paymentwall-payment" data-toggle="collapse"
                                            role="button" data-target="#collapse-paymentwall" aria-expanded="false"
                                            aria-controls="collapse-paymentwall">
                                            <h6 class="mb-0"><i
                                                    class="far fa-credit-card mr-3"></i>{{ __('PaymentWall') }}</h6>
                                        </div>
                                        <div id="collapse-paymentwall" class="collapse"
                                            aria-labelledby="paymentwall-payment" data-parent="#payment-gateways">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-6 py-2">

                                                        <small>
                                                            {{ __('Note: This detail will use for make checkout of plan.') }}</small>
                                                    </div>
                                                    <div class="col-6 py-2 text-right">
                                                        <div class="custom-control custom-switch float-right">
                                                            <input type="hidden" name="is_paymentwall_enabled"
                                                                value="off">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="is_paymentwall_enabled" id="is_paymentwall_enabled"
                                                                {{ isset($payment_detail['is_paymentwall_enabled']) && $payment_detail['is_paymentwall_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="custom-control-label form-control-label"
                                                                for="is_paymentwall_enabled">{{ __('Enable PaymentWall') }}</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="paymentwall_public_key"
                                                                class="form-control-label">{{ __('Public Key') }}</label>
                                                            <input type="text" name="paymentwall_public_key"
                                                                id="paymentwall_public_key" class="form-control"
                                                                value="{{ isset($payment_detail['paymentwall_public_key']) ? $payment_detail['paymentwall_public_key'] : '' }}"
                                                                placeholder="{{ __('Public Key') }}" />
                                                            @if ($errors->has('paymentwall_public_key'))
                                                                <span class="invalid-feedback d-block">
                                                                    {{ $errors->first('paymentwall_public_key') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="paymentwall_private_key"
                                                                class="form-control-label">{{ __('Private Key') }}</label>
                                                            <input type="text" name="paymentwall_private_key"
                                                                id="paymentwall_private_key"
                                                                class="form-control form-control-label"
                                                                value="{{ isset($payment_detail['paymentwall_private_key']) ? $payment_detail['paymentwall_private_key'] : '' }}"
                                                                placeholder="{{ __('Private Key') }}" />
                                                            @if ($errors->has('flutterwave_secret_key'))
                                                                <span class="invalid-feedback d-block">
                                                                    {{ $errors->first('paymentwall_private_key') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="text-right">
                                    {{ Form::hidden('from', 'payment') }}
                                    <button type="submit"
                                        class="btn btn-sm btn-primary rounded-pill">{{ __('Save changes') }}</button>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
            <div id="tabs-5" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <h5 class="h6 mb-0">{{ __('ReCaptcha Settings') }}</h5>
                        <small>{{ __('Test to tell human and bots apart by adding Recaptcha setting.') }}</small>
                    </div>
                    <div id="recaptcha-settings" class="tab-pane">
                        <div class="col-md-12">
                            <div class="row justify-content-between align-items-center">
                                <div class="col-md-6 col-sm-6 mb-3 mb-md-0">
                                    {{-- <h4 class="h4 font-weight-400 float-left pb-2">{{ __('ReCaptcha settings') }}</h4> --}}
                                </div>
                            </div>
                            <div class="p-3">
                                <form method="POST" action="{{ route('recaptcha.settings.store') }}"
                                    accept-charset="UTF-8">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input"
                                                    name="recaptcha_module" id="recaptcha_module" value="yes"
                                                    {{ env('RECAPTCHA_MODULE') == 'yes' ? 'checked="checked"' : '' }}>
                                                <label class="custom-control-label form-control-label"
                                                    for="recaptcha_module">
                                                    {{ __('Google Recaptcha') }}
                                                    <a href="https://phppot.com/php/how-to-get-google-recaptcha-site-and-secret-key/"
                                                        target="_blank" class="text-blue">
                                                        <small>({{ __('How to Get Google reCaptcha Site and Secret key') }})</small>
                                                    </a>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                            <label for="google_recaptcha_key"
                                                class="form-control-label">{{ __('Google Recaptcha Key') }}</label>
                                            <input class="form-control"
                                                placeholder="{{ __('Enter Google Recaptcha Key') }}"
                                                name="google_recaptcha_key" type="text"
                                                value="{{ env('NOCAPTCHA_SITEKEY') }}" id="google_recaptcha_key">
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                            <label for="google_recaptcha_secret"
                                                class="form-control-label">{{ __('Google Recaptcha Secret') }}</label>
                                            <input class="form-control "
                                                placeholder="{{ __('Enter Google Recaptcha Secret') }}"
                                                name="google_recaptcha_secret" type="text"
                                                value="{{ env('NOCAPTCHA_SECRET') }}" id="google_recaptcha_secret">
                                        </div>
                                    </div>
                                    <div class="col-lg-12  text-right">
                                        <input type="submit" value="{{ __('Save Changes') }}"
                                            class="btn btn-sm btn-primary rounded-pill">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        // For Sidebar Tabs
        $(document).ready(function() {
            $('.list-group-item').on('click', function() {
                var href = $(this).attr('data-href');
                $('.tabs-card').addClass('d-none');
                $(href).removeClass('d-none');
                $('#tabs .list-group-item').removeClass('text-primary');
                $(this).addClass('text-primary');
            });
        });

        // For Test Email Send
        $(document).on("click", '.send_email', function(e) {
            e.preventDefault();
            var title = $(this).attr('data-title');
            var size = 'md';
            var url = $(this).attr('data-url');
            if (typeof url != 'undefined') {
                $("#commonModal .modal-title").html(title);
                $("#commonModal .modal-dialog").addClass('modal-' + size);
                $("#commonModal").modal('show');

                $.post(url, {
                    mail_driver: $("#mail_driver").val(),
                    mail_host: $("#mail_host").val(),
                    mail_port: $("#mail_port").val(),
                    mail_username: $("#mail_username").val(),
                    mail_password: $("#mail_password").val(),
                    mail_encryption: $("#mail_encryption").val(),
                    mail_from_address: $("#mail_from_address").val(),
                    mail_from_name: $("#mail_from_name").val(),
                }, function(data) {
                    $('#commonModal .modal-body').html(data);
                });
            }
        });
        $(document).on('submit', '#test_email', function(e) {
            e.preventDefault();
            $("#email_sanding").show();
            var post = $(this).serialize();
            var url = $(this).attr('action');
            $.ajax({
                type: "post",
                url: url,
                data: post,
                cache: false,
                success: function(data) {
                    if (data.is_success) {
                        show_toastr('Success', data.message, 'success');
                    } else {
                        show_toastr('Error', data.message, 'error');
                    }
                    $("#email_sanding").hide();
                }
            });
        })
    </script>

    <script>
        $(document).ready(function() {
            if ($('.gdpr_fulltime').is(':checked')) {

                $('.fulltime').show();
            } else {

                $('.fulltime').hide();
            }

            $('#gdpr_cookie').on('change', function() {
                if ($('.gdpr_fulltime').is(':checked')) {

                    $('.fulltime').show();
                } else {

                    $('.fulltime').hide();
                }
            });
        });   
        
    </script>  

    <script>

        function check_theme(color_val) {                                
            $('input[value="' + color_val + '"]').prop('checked', true);
            $('input[value="' + color_val + '"]').attr('checked', true);
            $('a[data-value]').removeClass('active_color');
            $('a[data-value="' + color_val + '"]').addClass('active_color');
        }
        
    </script>


    
@endpush
