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
                    <h6 class="h3">{{ __('Verify Your Email Address') }}</h6>
                    @if (session('resent'))
                        <div class="badge badge-pill badge-primary" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif
                </div>
                <div class="card-footer px-md-5">
                    {{ __('Before proceeding, please check your email for a verification link.') }}
                    {{ __('If you did not receive the email') }},
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="small font-weight-bold">{{ __('click here to request another') }}</button>
                        .
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
