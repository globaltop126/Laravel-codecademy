@extends('layouts.auth')

@section('title')
    {{__('Login')}}
@endsection

@push('custom-scripts')
@if(env('RECAPTCHA_MODULE') == 'yes')
        {!! NoCaptcha::renderJs() !!}
@endif
@endpush

@section('language-bar')
    <div class="form-group auth-lang">
        <select name="language" id="language" class="form-control px-3" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
            @foreach(Utility::languages() as $language)
                <option @if($lang == $language) selected @endif value="{{ route('login',$language) }}">{{Str::upper($language)}}</option>
            @endforeach
        </select>
    </div>
@endsection

@section('content')
    <div class="col-sm-8 col-lg-4">
        <div class="text-center pb-4">
            <img src="{{ asset(Storage::url('logo/logo.png')) }}" class="w200">
        </div>
        <div class="card shadow zindex-100 mb-0">
            <div class="card-body px-md-5 py-5">
                <div class="mb-4">
                    <h6 class="h3">{{__('Login')}}</h6>
                    <p class="text-muted mb-0">{{__('Sign in to your account to continue.')}}</p>
                </div>
                <span class="clearfix"></span>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-control-label">{{__('Email address')}}</label>
                        <div class="input-group input-group-merge">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus/>
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <label class="form-control-label">{{__('Password')}}</label>
                            </div>
                            @if (Route::has('password.request'))
                                <div class="mb-2">
                                    <a href="{{ route('password.request',$lang) }}" class="small text-muted text-underline--dashed border-primary">{{__('Lost password?')}}</a>
                                </div>
                            @endif
                        </div>
                        <div class="input-group input-group-merge">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                            </div>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password"/>
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                            <div class="input-group-append">
                                <span class="input-group-text">
                                  <a href="#" data-toggle="password-text" data-target="#password">
                                    <i class="fas fa-eye"></i>
                                  </a>
                                </span>
                            </div>
                        </div>
                        <div class="my-4">
                            <div class="custom-control custom-checkbox mb-3">
                                <input type="checkbox" class="custom-control-input" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="remember">{{ __('Remember Me') }}</label>
                            </div>
                        </div>
                        @if(env('RECAPTCHA_MODULE') == 'yes')
                            <div class="form-group col-lg-12 col-md-12 mt-3">
                                {!! NoCaptcha::display() !!}
                                @error('g-recaptcha-response')
                                <span class="small text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        @endif

                        <div class="mt-4">
                            <button type="submit" class="btn btn-sm btn-primary btn-icon rounded-pill">
                                <span class="btn-inner--text">{{__('Sign in')}}</span>
                                <span class="btn-inner--icon"><i class="fas fa-long-arrow-alt-right"></i></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @if(Utility::getValByName('SIGNUP') == 'on')
                <div class="card-footer px-md-5"><small>{{__('Not registered?')}}</small>
                    <a href="{{ route('register',$lang) }}" class="small font-weight-bold">{{__('Create account')}}</a>
                </div>
            @endif
        </div>
    </div>
@endsection
