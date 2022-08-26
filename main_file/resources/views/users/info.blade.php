@extends('layouts.admin')

@section('title')
    {{ (\Auth::user()->id != $user->id) ? $user->name.__("'s Overview") : __('My Overview')  }}
@endsection

@section('action-button')
@endsection

@push('theme-script')
    <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-{{($role['role'] == 'Client') ? '6':'4'}}">
            <div class="card card-fluid">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <a href="#" class="avatar rounded-circle">
                                <img {{ $user->img_avatar }} >
                            </a>
                        </div>
                        <div class="col ml-md-n2">
                            <a href="#!" class="d-block h6 mb-0">{{ $user->name }} <span class="badge badge-xs badge-{{$role['color']}} ml-2">{{$role['role']}}</span></a>
                            <small class="d-block text-muted">{{ $user->email }}</small>
                            <small class="d-block text-muted">{{ (!empty($user->phone)) ? $user->phone : ''}}</small>
                        </div>
                        @if(Auth::user()->id == $user->id)
                            <div class="col-auto">
                                <a href="{{ route('profile') }}">
                                    <button type="button" class="btn btn-xs btn-primary btn-icon rounded-pill">
                                        <span class="btn-inner--icon"><i class="fas fa-edit"></i></span>
                                        <span class="btn-inner--text">{{__('Edit')}}</span>
                                    </button>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(!empty($user->skills))
                        @foreach(explode(',',$user->skills) as $skill)
                            <span class="badge badge-pill badge-primary d-inline-block mt-2">{{$skill}}</span>
                        @endforeach
                    @else
                        <span>{{__('No Skills Found..!')}}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-{{($role['role'] == 'Client') ? '6':'4'}}">
            <div class="card card-fluid">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-12">
                            <h6 class="text-sm mb-0">
                                <i class="fab fa-facebook mr-2"></i>{{__('Facebook')}}
                            </h6>
                        </div>
                        <div class="col-12">
                            <span class="text-sm">{{ (!empty($user->facebook) ? $user->facebook : '-') }}</span>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="row align-items-center">
                        <div class="col-12">
                            <h6 class="text-sm mb-0">
                                <i class="fab fa-whatsapp mr-2"></i>{{__('WhatsApp')}}
                            </h6>
                        </div>
                        <div class="col-12">
                            <span class="text-sm">{{ (!empty($user->whatsapp) ? $user->whatsapp : '-') }}</span>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="row align-items-center">
                        <div class="col-12">
                            <h6 class="text-sm mb-0">
                                <i class="fab fa-instagram mr-2"></i>{{__('Instagram')}}
                            </h6>
                        </div>
                        <div class="col-12">
                            <span class="text-sm">{{ (!empty($user->instagram) ? $user->instagram : '-') }}</span>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="row align-items-center">
                        <div class="col-12">
                            <h6 class="text-sm mb-0">
                                <i class="fab fa-linkedin mr-2"></i>{{__('LinkedIn')}}
                            </h6>
                        </div>
                        <div class="col-12">
                            <span class="text-sm">{{ (!empty($user->likedin) ? $user->likedin : '-') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($role['role'] != 'Client')
            <div class="col-lg-4">
                <div class="card card-fluid">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-sm mb-0">{{__('Time Logged on Timesheet')}}</h6>
                                <span class="text-nowrap h6 text-muted text-sm">{{ $user_data['timesheet_timelog'] }}</span>
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-sm mb-0">{{__('Total Completed Task')}}</h6>
                                <span class="text-nowrap h6 text-muted text-sm">{{ $user_data['complete_task'] }}</span>
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-sm mb-0">{{__('Total Open Task')}}</h6>
                                <span class="text-nowrap h6 text-muted text-sm">{{ $user_data['open_task'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="row">
        @if($role['role'] != 'Client')
            <div class="col-xl-8 col-md-6">
                <div class="card card-fluid">
                    <div class="card-header">
                        <h6 class="mb-0">{{__('Timesheet Logged Hours')}}</h6>
                        <small class="text-muted">{{__('Last 7 days')}}</small>
                    </div>
                    <div class="card-body">
                        <div id="timesheet_logged_hrs" data-color="primary" data-height="410"></div>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-xl-{{($role['role'] == 'Client') ? '6':'4'}} col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">{{__('My Projects')}}</h6>
                        </div>
                    </div>
                </div>
                @include('users.project')
            </div>
        </div>
        @if($role['role'] == 'Client')
            <div class="col-lg-6">
                <div class="card card-fluid">
                    <div class="card-header">
                        <h6 class="mb-0">{{__('Top Due Tasks')}}</h6>
                    </div>
                    <div class="card-body">
                        @include('users.due_tasks')
                    </div>
                </div>
            </div>
        @endif
    </div>
    @if($role['role'] != 'Client')
        <div class="row">
            <div class="col-lg-4">
                <div class="card card-fluid">
                    <div class="card-header">
                        <h6 class="mb-0">{{__('Total Tasks Report')}}</h6>
                    </div>
                    <div class="card-body">
                        <div class="progress-circle progress-lg mx-auto" data-progress="{{ $user_data['task_report']['percentage'] }}" data-text="{{ $user_data['task_report']['percentage'] }}%" data-color="warning"></div>
                        <div class="d-flex my-4 px-5 text-center">
                            <div class="col">
                            <span class="badge badge-dot badge-lg h6">
                                <i class="bg-warning"></i>{{ $user_data['task_report']['done'] }}
                            </span>
                                <small class="d-block text-muted">{{__('Done')}}</small>
                            </div>
                            <div class="col">
                            <span class="badge badge-dot badge-lg h6">
                                <i class="bg-success"></i>{{ $user_data['task_report']['open'] }}
                            </span>
                                <small class="d-block text-muted">{{__('Open')}}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card card-fluid">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{__('Recent Attachments')}}</h6>
                                <small class="text-muted">{{__('That uploaded for this user\'s assigned project')}}</small>
                            </div>
                        </div>
                    </div>
                    <div class="scrollbar-inner">
                        <div class="min-h-300 mh-300">
                            <div class="card-wrapper p-3">
                                @if($user_data['task_files']->count() > 0)
                                    @foreach($user_data['task_files'] as $task_file)
                                        <div class="card mb-3 border shadow-none">
                                            <div class="px-3 py-3">
                                                <div class="row align-items-center">
                                                    <div class="col-auto">
                                                        <img src="{{ asset('assets/img/icons/files/'.$task_file->extension.'.png') }}" class="img-fluid" style="width: 40px;">
                                                    </div>
                                                    <div class="col ml-n2">
                                                        <h6 class="text-sm mb-0">
                                                            <a href="#!">{{ $task_file->name }}</a>
                                                        </h6>
                                                        <p class="card-text small text-muted">
                                                            {{ $task_file->file_size }}
                                                        </p>
                                                    </div>
                                                    <div class="col-auto actions">
                                                        <a href="{{asset(Storage::url('tasks/'.$task_file->file))}}" download class="action-item" role="button">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="py-5">
                                        <h6 class="h6 text-center">{{__('No Attachments Found.')}}</h6>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card card-fluid">
                    <div class="card-header">
                        <h6 class="mb-0">{{__('Top Due Tasks')}}</h6>
                    </div>
                    <div class="card-body">
                        @include('users.due_tasks')
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('script')
    @if($role['role'] != 'Client')
        <script>
            var e = $("#timesheet_logged_hrs");
            var t = {
                chart: {width: "100%", type: "bar", zoom: {enabled: !1}, toolbar: {show: !1}, shadow: {enabled: !1}},
                plotOptions: {bar: {horizontal: !1, columnWidth: "30%", endingShape: "rounded"}},
                stroke: {show: !0, width: 2, colors: ["transparent"]},
                series: [{name: "Timesheet hours ", data: {!! json_encode(array_values($user_data['timesheet_chart'])) !!}}],
                xaxis: {labels: {style: {colors: SiteStyle.colors.gray[600], fontSize: "14px", fontFamily: SiteStyle.fonts.base, cssClass: "apexcharts-xaxis-label"}}, axisBorder: {show: !1}, axisTicks: {show: !0, borderType: "solid", color: SiteStyle.colors.gray[300], height: 6, offsetX: 0, offsetY: 0}, type: "category", categories: {!! json_encode(array_keys($user_data['timesheet_chart'])) !!}},
                yaxis: {labels: {style: {color: SiteStyle.colors.gray[600], fontSize: "12px", fontFamily: SiteStyle.fonts.base}}, axisBorder: {show: !1}, axisTicks: {show: !0, borderType: "solid", color: SiteStyle.colors.gray[300], height: 6, offsetX: 0, offsetY: 0}},
                fill: {type: "solid"},
                markers: {size: 4, opacity: .7, strokeColor: "#fff", strokeWidth: 3, hover: {size: 7}},
                grid: {borderColor: SiteStyle.colors.gray[300], strokeDashArray: 5},
                dataLabels: {enabled: !1}
            }, a = (e.data().dataset, e.data().labels, e.data().color), n = e.data().height;
            e.data().type, t.colors = [SiteStyle.colors.theme[a]], t.markers.colors = [SiteStyle.colors.theme[a]], t.chart.height = n || 350;
            var o = new ApexCharts(e[0], t);
            setTimeout(function () {
                o.render()
            }, 300);
        </script>
    @endif
@endpush


