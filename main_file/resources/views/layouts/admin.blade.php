<!doctype html>
{{-- <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" {{(\App\Models\Utility::getValByName('enable_rtl') == 'on') ? 'dir="rtl"' : ''}}> --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir={{ \App\Models\Utility::getValByName('enable_rtl') == 'on' ? 'rtl' : 'ltr' }}>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title') &dash;
        {{ Utility::getValByName('header_text') ? Utility::getValByName('header_text') : config('app.name') }}</title>
    <link rel="icon" href="{{ asset(Storage::url('logo/favicon.png')) }}" type="image/png">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('assets/libs/@fortawesome/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/animate.css/animate.min.css') }}">

    

    @if (Auth::user()->mode == 'light')
        @include('layouts.lightthemecolor')
        <link rel="stylesheet" href="{{ asset('assets/css/site-light.css') }}">
    @else
        @include('layouts.darkthemecolor')
        <link rel="stylesheet" href="{{ asset('assets/css/site-dark.css') }}">
    @endif


    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}">

    @if (\App\Models\Utility::getValByName('enable_rtl') == 'on')
        
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-rtl.css') }}">
    @endif
    
    <meta name="url" content="{{ url('') . '/' . config('chatify.routes.prefix') }}"
        data-user="{{ Auth::user()->id }}">
    <title>{{ __('Messages') }} &dash;
        {{ Utility::getValByName('header_text') ? Utility::getValByName('header_text') : config('app.name') }}
    </title>
    <link rel="icon" href="{{ asset(Storage::url('logo/favicon.png')) }}" type="image/png">

    {{-- scripts --}}
    <script src="{{ asset('js/chatify/font.awesome.min.js') }}"></script>
    <script src="{{ asset('js/chatify/autosize.js') }}"></script>
    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}
    <script src='https://unpkg.com/nprogress@0.2.0/nprogress.js'></script>

    {{-- styles --}}
    <link rel='stylesheet' href='https://unpkg.com/nprogress@0.2.0/nprogress.css' />
    <link href="{{ asset('css/chatify/style.css') }}" rel="stylesheet" />
    @php
        $unseenCounter = App\Models\ChMessage::where('to_id', Auth::user()->id)
            ->where('seen', 0)
            ->count();
    @endphp

    @stack('css')
</head>

<body class="application application-offset ">
    @if (\Auth::user()->type != 'admin')
        @php
            $trackerdata = \App\Models\ProjectTask::lastTime();
        @endphp
    @endif
    <div class="container-fluid container-application">
        <div class="sidenav pb-2" id="sidenav-main">
            @include('partials.admin.sidebar')
        </div>
        <div class="main-content position-relative">
            @include('partials.admin.navbar')
            <div class="page-content">
                @if (trim($__env->yieldContent('title')) != 'Task Calendar')
                    <div class="page-title">
                        <div class="row justify-content-between align-items-center">
                            <div
                                class="col-xs-12 col-sm-12 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
                                <div class="d-inline-block">
                                    <h5 class="h4 d-inline-block font-weight-400 mb-0 text-white">@yield('title')
                                        @if (trim($__env->yieldContent('role')))
                                            <span class="text text-sm text-white m-0 ml-2">(@yield('role'))</span>
                                        @endif
                                    </h5>
                                </div>
                            </div>
                            <div
                                class="col-xs-12 col-sm-12 col-md-8 d-md-flex d-block align-items-center justify-content-between justify-content-md-end rounded_icon">
                                @yield('action-button')
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="footer pt-5 pb-4 footer-light" id="footer-main">
                    <div class="row text-center text-sm-left align-items-sm-center">
                        <div class="col-sm-6">
                            <p class="text-sm mb-0">{{ \App\Models\Utility::getValByName('footer_text') }}</p>
                        </div>
                        <div class="col-sm-6 mb-md-0">
                            <ul class="nav justify-content-center justify-content-md-end">
                                <li class="nav-item dropdown border-right">
                                    <a class="nav-link" href="#" role="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <span class="h6 text-sm mb-0"><i class="fas fa-globe-asia"></i>
                                            {{ Str::upper(Auth::user()->lang) }}</span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                        @foreach (\App\Models\Utility::languages() as $lang)
                                            <a href="{{ route('lang.change', $lang) }}"
                                                class="dropdown-item {{ basename(App::getLocale()) == $lang ? 'active' : '' }} ">{{ Str::upper($lang) }}</a>
                                        @endforeach
                                    </div>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ route('change.mode') }}">{{ Auth::user()->mode == 'light' ? __('Dark Mode') : __('Light Mode') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ \App\Models\Utility::getValByName('footer_value_1') }}">{{ \App\Models\Utility::getValByName('footer_link_1') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ \App\Models\Utility::getValByName('footer_value_2') }}">{{ \App\Models\Utility::getValByName('footer_link_2') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ \App\Models\Utility::getValByName('footer_value_3') }}">{{ \App\Models\Utility::getValByName('footer_link_3') }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Common Modal --}}
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
    {{-- End Common Modal --}}

    {{-- Side Modal --}}
    <div class="modal fade fixed-right" id="commonModal-right" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="scrollbar-inner">
            <div class="min-h-300 mh-300">
            </div>
        </div>
    </div>
    {{-- Side Modal End --}}

    <!-- Omnisearch -->
    <div id="omnisearch" class="omnisearch">
        <div class="container">
            <div class="omnisearch-form">
                <div class="form-group">
                    <div class="input-group input-group-merge input-group-flush">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" class="form-control search_keyword"
                            placeholder="{{ __('Search by Task Name..') }}">
                    </div>
                </div>
            </div>
            <div class="omnisearch-suggestions">
                <h6 class="heading">{{ __('Search Suggestions') }}</h6>
                <div class="row">
                    <div class="col-sm-12">
                        <ul class="list-unstyled mb-0 search_output">
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

<!-- Scripts -->
<script src="{{ asset('assets/js/site.core.js') }}"></script>
<script src="{{ asset('assets/libs/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('assets/libs/progressbar.js/dist/progressbar.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
<script src="{{ asset('assets/libs/autosize/dist/autosize.min.js') }}"></script>
<script src="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker.js') }}"></script>

@stack('theme-script')
<script src="{{ asset('assets/js/site.js') }}"></script>
<script src="{{ asset('assets/js/letter.avatar.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>

@if (Utility::getValByName('gdpr_cookie') == 'on')
    <script type="text/javascript">
        var defaults = {
            'messageLocales': {
                /*'en': 'We use cookies to make sure you can have the best experience on our website. If you continue to use this site we assume that you will be happy with it.'*/
                'en': "{{ Utility::getValByName('cookie_text') }}"
            },
            'buttonLocales': {
                'en': 'Ok'
            },
            'cookieNoticePosition': 'bottom',
            'learnMoreLinkEnabled': false,
            'learnMoreLinkHref': '/cookie-banner-information.html',
            'learnMoreLinkText': {
                'it': 'Saperne di più',
                'en': 'Learn more',
                'de': 'Mehr erfahren',
                'fr': 'En savoir plus'
            },
            'buttonLocales': {
                'en': 'Ok'
            },
            'expiresIn': 30,
            'buttonBgColor': '#d35400',
            'buttonTextColor': '#fff',
            'noticeBgColor': '#051c4b',
            'noticeTextColor': '#fff',
            'linkColor': '#009fdd'
        };
    </script>
    <script src="{{ asset('assets/js/cookie.notice.js') }}"></script>
@endif

@stack('script')

@if (Session::has('success'))
    <script>
        show_toastr('{{ __('Success') }}', '{!! session('success') !!}', 'success');
    </script>
    {{ Session::forget('success') }}
@endif
@if (Session::has('error'))
    <script>
        show_toastr('{{ __('Error') }}', '{!! session('error') !!}', 'error');
    </script>
    {{ Session::forget('error') }}
@endif

<script>
    @if (\Auth::user()->type != 'admin')
        $(document).ready(function() {
            search_data();
            $(document).on('keyup', '.search_keyword', function() {
                search_data($(this).val());
            });
        });

        // Common main search
        function search_data(keyword = '') {
            $.ajax({
                url: '{{ route('search.json') }}',
                data: {
                    keyword: keyword
                },
                success: function(data) {
                    $('.search_output').html(data);
                }
            });
        }

        // Common TimeTracker
        var timer = '';
        var timzone = '{{ env('TIMEZONE') }}';

        function TrackerTimer(start_time) {
            timer = setInterval(function() {
                var start = new Date(start_time);

                var here = new Date();
                var end = changeTimezone(here, timzone);

                var hrs = end.getHours() - start.getHours();
                var min = end.getMinutes() - start.getMinutes();
                var sec = end.getSeconds() - start.getSeconds();
                var hour_carry = 0;
                var Timer = $(".timer-counter");
                var minutes_carry = 0;
                if (min < 0) {
                    min += 60;
                    hour_carry += 1;
                }
                hrs = hrs - hour_carry;
                if (sec < 0) {
                    sec += 60;
                    minutes_carry += 1;
                }
                min = min - minutes_carry;
                Timer.text(minTwoDigits(hrs) + ':' + minTwoDigits(min) + ':' + minTwoDigits(sec));
            }, 1000);
        }

        function changeTimezone(date, ianatz) {
            var invdate = new Date(date.toLocaleString('en-US', {
                timeZone: ianatz
            }));
            var diff = date.getTime() - invdate.getTime();
            return new Date(date.getTime() - diff);
        }

        function minTwoDigits(n) {
            return (n < 10 ? '0' : '') + n;
        }
    @endif
</script>
@if (\Auth::user()->type != 'admin')
    @if (count($trackerdata) > 0)
        <script>
            TrackerTimer("{{ $trackerdata['start_time'] }}");
            $('.start-task').html("{{ $trackerdata['title'] }}");
        </script>
    @endif
@endif
@include('Chatify::layouts.footerLinks')

</html>
