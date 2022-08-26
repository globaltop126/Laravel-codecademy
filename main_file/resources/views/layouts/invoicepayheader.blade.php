@php
    // $currantLang = $users->currentLanguage();
    $languages=\App\Models\Utility::languages();
    $footer_text=isset(\App\Models\Utility::settings()['footer_text']) ? \App\Models\Utility::settings()['footer_text'] : '';
    $header_text = (!empty(\App\Models\Utility::settings()['company_name'])) ? \App\Models\Utility::settings()['company_name'] : env('APP_NAME');
	$SITE_RTL = Cookie::get('SITE_RTL');
    
    if($SITE_RTL != 'on'){
        $SITE_RTL = 'off';
    }
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir={{(\App\Models\Utility::getValByName('enable_rtl') == 'on') ? 'rtl' : 'ltr'}}>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') &dash; {{ config('app.name', 'TaskGo SaaS') }}</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    {{-- <script src="{{ asset('assets/js/custom.js') }}"></script> --}}
    <link rel="icon" href="{{ asset(Storage::url('logo/favicon.png')) }}" type="image/png">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/libs/@fortawesome/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/site-light.css') }}" id="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    @if(\App\Models\Utility::getValByName('enable_rtl') == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-rtl.css') }}">
    @endif
</head>
<body class="application application-offset">
    <div class="container-fluid container-application">
            <div class="page-content">
                @if (trim($__env->yieldContent('title')) != 'Task Calendar')
                    <div class="page-title">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-xs-12 col-sm-12 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
                                <div class="d-inline-block">
                                    <h5 class="h4 d-inline-block font-weight-400 mb-0 text-white">@yield('title')</h5>
                                    
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
                                @yield('action-button')
                            </div>
                        </div>
                    </div>
                @endif
                @yield('content')
            </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="commonModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>

    <div class="modal fade fixed-right" id="commonModal-right" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="scrollbar-inner">
            <div class="min-h-300 mh-300">
            </div>
        </div>
    </div>
    
    
    </body>

    
    <script src="{{asset('assets/js/site.core.js')}}"></script>
    <script src="{{url('assets/js/jquery.form.js')}}"></script>
    <script src="{{asset('assets/js/site.js')}}"></script>

    <script src="{{ asset('assets/js/custom.js') }}"></script>

    @if(Session::has('success'))
    <script>
        show_toastr('{{__('Success')}}', '{!! session('success') !!}', 'success');
    </script>
    {{ Session::forget('success') }}
    @endif
    @if(Session::has('error'))
        <script>
            show_toastr('{{__('Error')}}', '{!! session('error') !!}', 'error');
        </script>
        {{ Session::forget('error') }}
    @endif
   
    
    @stack('script')