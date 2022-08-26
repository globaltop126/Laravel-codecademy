@extends('layouts.auth')

@section('title')
    {{__('Password reset')}}
@endsection

@section('content')
    <div class="col-sm-8 col-lg-5 col-xl-4">
        <div class="text-center pb-4">
            <img src="{{ asset(Storage::url('logo/logo.png')) }}" class="w200">
        </div>
        <div class="card shadow zindex-100 mb-0">
            <div class="card-body px-md-5 py-5">
                <div class="mb-5">
                    <h6 class="h3">{{ __('Confirm Password') }}</h6>
                    <p class="text-muted mb-0">{{ __('Please confirm your password before continuing.') }}</p>
                </div>
                <span class="clearfix"></span>
                <form role="form">
                    <div class="form-group">
                        <label class="form-control-label">{{__('Password')}}</label>
                        <div class="input-group input-group-merge">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                            </div>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-sm btn-primary btn-icon rounded-pill">
                            <span class="btn-inner--text">{{ __('Confirm Password') }}</span>
                            <span class="btn-inner--icon"><i class="fas fa-long-arrow-alt-right"></i></span>
                        </button>
                    </div>
                </form>
            </div>
            @if (Route::has('password.request'))
                <div class="card-footer px-md-5"><small>{{__('Already have an acocunt?')}}</small>
                    <a href="{{ route('password.request') }}" class="small font-weight-bold">{{ __('Forgot Your Password?') }}</a>
                </div>
            @endif
        </div>
    </div>
@endsection
