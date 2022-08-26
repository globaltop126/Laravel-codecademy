@extends('layouts.admin')

@section('title')
    {{$project->name}}
@endsection

@section('role'){{__('You are ') .__(ucfirst($project->permission()))}}@endsection

@php
    $permissions = \Auth::user()->getPermission($project->id);
@endphp

@section('action-button')
    <div class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">
        @if(isset($permissions) && in_array('project setting',$permissions))
            <a href="{{ route('projects.edit',$project) }}" class="btn btn-sm btn-white rounded-circle btn-icon-only ml-0">
                <span class="btn-inner--icon"><i class="fas fa-cog"></i></span>
            </a>
        @endif
        <a href="{{ route('projects.tasks.index',$project->id) }}" class="btn btn-sm bg-white btn-icon rounded-pill ml-0">
            <span class="btn-inner--text text-dark">{{__('Tasks')}}</span>
        </a>
       
        <a href="{{ route('timesheet.index',$project->id) }}" class="btn btn-sm bg-white btn-icon rounded-pill ml-0">
            <span class="btn-inner--text text-dark">{{__('Timesheet')}}</span>
        </a>
        <a href="{{ route('projects.time.tracker',$project->id) }}" class="btn btn-sm bg-white btn-icon rounded-pill ml-0">
            <span class="btn-inner--text text-dark">{{__('Tracker')}}</span>
        </a>
        <a href="{{ route('projects.gantt',$project->id) }}" class="btn btn-sm bg-white btn-icon rounded-pill ml-2">
            <span class="btn-inner--text text-dark">{{__('Gantt Chart')}}</span>
        </a>
        @if(isset($permissions) && (in_array('show expense',$permissions) || in_array('create expense',$permissions)))
            <a href="{{ route('projects.expenses.index',$project->id) }}" class="btn btn-sm bg-white btn-icon rounded-pill ml-0">
                <span class="btn-inner--text text-dark">{{__('Expense')}}</span>
            </a>
        @endif
    </div>
@endsection

@push('theme-script')
    <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
@endpush

@section('content')
    <div class="row">
        @if($project->permission() == 'client')
            <div class="col-xl-3 col-sm-6">
                <div class="card card-stats border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h6 class="text-muted mb-1">{{ __('Task Done')  }}</h6>
                                <span class="h4 font-weight-bold mb-0 ">{{ $project_data['task']['done'] }}</span>
                            </div>
                            <div class="col-auto">
                                <div class="progress-circle progress-sm" data-progress="{{ $project_data['task']['percentage'] }}" data-text="{{ $project_data['task']['percentage'] }}%" data-color="primary"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <span class="text-sm text-muted">{{ __('Total Task').' : '.$project_data['task']['total'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                 
                <div class="card">
                    <div class="card-body">
                        <div class="">
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="mb-0">{{ $project_data['task_chart']['total'] }}</h6>
                                    <span class="text-sm text-muted">{{__('Last 7 days task done')}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="w-100 pt-4 pb-5">
                            <div class="spark-chart" data-toggle="spark-chart" data-color="info" data-dataset="{{ json_encode($project_data['task_chart']['chart']) }}"></div>
                        </div>
                        <div class="progress-wrapper mb-3">
                            <small class="progress-label">{{ __('Day Left') }} <span class="text-muted">{{ $project_data['day_left']['day'] }}</span></small>
                            <div class="progress mt-0 height-3">
                                <div class="progress-bar bg-info" role="progressbar" aria-valuenow="{{ $project_data['day_left']['percentage'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $project_data['day_left']['percentage'] }}%;"></div>
                            </div>
                        </div>
                        <div class="progress-wrapper">
                            <small class="progress-label">{{__('Open Task')}} <span class="text-muted">{{ $project_data['open_task']['tasks'] }}</span></small>
                            <div class="progress mt-0 height-3">
                                <div class="progress-bar bg-info" role="progressbar" aria-valuenow="{{ $project_data['open_task']['percentage'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $project_data['open_task']['percentage'] }}%;"></div>
                            </div>
                        </div>
                        <div class="progress-wrapper">
                            <small class="progress-label">{{__('Completed Milestone')}} <span class="text-muted">{{ $project_data['milestone']['total'] }}</span></small>
                            <div class="progress mt-0" style="height: 3px;">
                                <div class="progress-bar bg-info" role="progressbar" aria-valuenow="{{ $project_data['milestone']['percentage'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $project_data['milestone']['percentage'] }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card card-stats">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h6 class="text-muted mb-1">{{ __('Expense') }}</h6>
                                <span class="h4 font-weight-bold mb-0 ">{{ \App\Models\Utility::projectCurrencyFormat($project->id,$project_data['expense']['total']) }}</span>
                            </div>
                            <div class="col-auto">
                                <div class="progress-circle progress-sm" data-progress="{{ $project_data['expense']['percentage'] }}" data-text="{{ $project_data['expense']['percentage'] }}%" data-color="primary"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <span class="text-sm text-muted">{{ __('Total Budget').' : '.\App\Models\Utility::projectCurrencyFormat($project->id,$project_data['expense']['allocated']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mb-0">{{ $project_data['timesheet_chart']['total'] }}</h6>
                                <span class="text-sm text-muted">{{__('Last 7 days hours spent')}}</span>
                            </div>
                        </div>
                        <div class="w-100 pt-4 pb-5">
                            <div class="spark-chart" data-toggle="spark-chart" data-color="warning" data-dataset="{{ json_encode($project_data['timesheet_chart']['chart']) }}"></div>
                        </div>
                        <div class="progress-wrapper mb-3">
                            <small class="progress-label">{{__('Total project time spent')}} <span class="text-muted">{{ $project_data['time_spent']['total'] }}</span></small>
                            <div class="progress mt-0 height-3">
                                <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="{{ $project_data['time_spent']['percentage'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $project_data['time_spent']['percentage'] }}%;"></div>
                            </div>
                        </div>
                        <div class="progress-wrapper">
                            <small class="progress-label">{{__('Allocated hours on task')}} <span class="text-muted">{{ $project_data['task_allocated_hrs']['hrs'] }}</span></small>
                            <div class="progress mt-0 height-3">
                                <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="{{ $project_data['task_allocated_hrs']['percentage'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $project_data['task_allocated_hrs']['percentage'] }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(\Auth::user()->checkProject($project->id) == 'Owner')
            <div class="col-xl-3 col-sm-6">
                <div class="card card-stats border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h6 class="text-muted mb-1">{{ __('Task Done')  }}</h6>
                                <span class="h4 font-weight-bold mb-0 ">{{ $project_data['task']['done'] }}</span>
                            </div>
                            <div class="col-auto">
                                <div class="progress-circle progress-sm" data-progress="{{ $project_data['task']['percentage'] }}" data-text="{{ $project_data['task']['percentage'] }}%" data-color="primary"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <span class="text-sm text-muted">{{ __('Total Task').' : '.$project_data['task']['total'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
               
                <div class="card">
                    <div class="card-body">
                        <div class="">
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="mb-0">{{ $project_data['task_chart']['total'] }}</h6>
                                    <span class="text-sm text-muted">{{__('Last 7 days task done')}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="w-100 pt-4 pb-5">
                            <div class="spark-chart" data-toggle="spark-chart" data-color="info" data-dataset="{{ json_encode($project_data['task_chart']['chart']) }}"></div>
                        </div>
                        <div class="progress-wrapper mb-3">
                            <small class="progress-label">{{ __('Day Left') }} <span class="text-muted">{{ $project_data['day_left']['day'] }}</span></small>
                            <div class="progress mt-0 height-3">
                                <div class="progress-bar bg-info" role="progressbar" aria-valuenow="{{ $project_data['day_left']['percentage'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $project_data['day_left']['percentage'] }}%;"></div>
                            </div>
                        </div>
                        <div class="progress-wrapper">
                            <small class="progress-label">{{__('Open Task')}} <span class="text-muted">{{ $project_data['open_task']['tasks'] }}</span></small>
                            <div class="progress mt-0 height-3">
                                <div class="progress-bar bg-info" role="progressbar" aria-valuenow="{{ $project_data['open_task']['percentage'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $project_data['open_task']['percentage'] }}%;"></div>
                            </div>
                        </div>
                        <div class="progress-wrapper">
                            <small class="progress-label">{{__('Completed Milestone')}} <span class="text-muted">{{ $project_data['milestone']['total'] }}</span></small>
                            <div class="progress mt-0" style="height: 3px;">
                                <div class="progress-bar bg-info" role="progressbar" aria-valuenow="{{ $project_data['milestone']['percentage'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $project_data['milestone']['percentage'] }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card card-stats">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h6 class="text-muted mb-1">{{ __('Expense') }}</h6>
                                <span class="h4 font-weight-bold mb-0 ">{{ \App\Models\Utility::projectCurrencyFormat($project->id,$project_data['expense']['total']) }}</span>
                            </div>
                            <div class="col-auto">
                                <div class="progress-circle progress-sm" data-progress="{{ $project_data['expense']['percentage'] }}" data-text="{{ $project_data['expense']['percentage'] }}%" data-color="primary"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <span class="text-sm text-muted">{{ __('Total Budget').' : '.\App\Models\Utility::projectCurrencyFormat($project->id,$project_data['expense']['allocated']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mb-0">{{ $project_data['timesheet_chart']['total'] }}</h6>
                                <span class="text-sm text-muted">{{__('Last 7 days hours spent')}}</span>
                            </div>
                        </div>
                        <div class="w-100 pt-4 pb-5">
                            <div class="spark-chart" data-toggle="spark-chart" data-color="warning" data-dataset="{{ json_encode($project_data['timesheet_chart']['chart']) }}"></div>
                        </div>
                        <div class="progress-wrapper mb-3">
                            <small class="progress-label">{{__('Total project time spent')}} <span class="text-muted">{{ $project_data['time_spent']['total'] }}</span></small>
                            <div class="progress mt-0 height-3">
                                <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="{{ $project_data['time_spent']['percentage'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $project_data['time_spent']['percentage'] }}%;"></div>
                            </div>
                        </div>
                        <div class="progress-wrapper">
                            <small class="progress-label">{{__('Allocated hours on task')}} <span class="text-muted">{{ $project_data['task_allocated_hrs']['hrs'] }}</span></small>
                            <div class="progress mt-0 height-3">
                                <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="{{ $project_data['task_allocated_hrs']['percentage'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $project_data['task_allocated_hrs']['percentage'] }}%;"></div>
                            </div>
                        </div>
                        <div class="progress-wrapper">
                            <small class="progress-label">{{__('User Assigned')}} <span class="text-muted">{{ $project_data['user_assigned']['total'] }}</span></small>
                            <div class="progress mt-0 height-3">
                                <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="{{ $project_data['user_assigned']['percentage'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $project_data['user_assigned']['percentage'] }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="">
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="mb-0">{{ $project_data['task_chart']['total'] }}</h6>
                                    <span class="text-sm text-muted">{{__('Last 7 days task done')}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="w-100 pt-4 pb-5">
                            <div class="spark-chart" data-toggle="spark-chart" data-color="info" data-dataset="{{ json_encode($project_data['task_chart']['chart']) }}"></div>
                        </div>
                        <div class="progress-wrapper mb-3">
                            <small class="progress-label">{{ __('Day Left') }} <span class="text-muted">{{ $project_data['day_left']['day'] }}</span></small>
                            <div class="progress mt-0 height-3">
                                <div class="progress-bar bg-info" role="progressbar" aria-valuenow="{{ $project_data['day_left']['percentage'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $project_data['day_left']['percentage'] }}%;"></div>
                            </div>
                        </div>
                        <div class="progress-wrapper">
                            <small class="progress-label">{{__('Open Task')}} <span class="text-muted">{{ $project_data['open_task']['tasks'] }}</span></small>
                            <div class="progress mt-0 height-3">
                                <div class="progress-bar bg-info" role="progressbar" aria-valuenow="{{ $project_data['open_task']['percentage'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $project_data['open_task']['percentage'] }}%;"></div>
                            </div>
                        </div>
                        <div class="progress-wrapper">
                            <small class="progress-label">{{__('Completed Milestone')}} <span class="text-muted">{{ $project_data['milestone']['total'] }}</span></small>
                            <div class="progress mt-0" style="height: 3px;">
                                <div class="progress-bar bg-info" role="progressbar" aria-valuenow="{{ $project_data['milestone']['percentage'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $project_data['milestone']['percentage'] }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mb-0">{{ $project_data['timesheet_chart']['total'] }}</h6>
                                <span class="text-sm text-muted">{{__('Last 7 days hours spent')}}</span>
                            </div>
                        </div>
                        <div class="w-100 pt-4 pb-5">
                            <div class="spark-chart" data-toggle="spark-chart" data-color="warning" data-dataset="{{ json_encode($project_data['timesheet_chart']['chart']) }}"></div>
                        </div>
                        <div class="progress-wrapper mb-3">
                            <small class="progress-label">{{__('Total project time spent')}} <span class="text-muted">{{ $project_data['time_spent']['total'] }}</span></small>
                            <div class="progress mt-0 height-3">
                                <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="{{ $project_data['time_spent']['percentage'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $project_data['time_spent']['percentage'] }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{--Project Overview--}}
        <div class="col-sm-6">
            <div class="card card-fluid">
                <div class="card-header">
                    <h6 class="mb-0">{{__('Project overview')}}</h6>
                </div>
                <div class="card-body py-3 flex-grow-1">
                    <div class="pb-3 mb-3 border-bottom">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <img {{ $project->img_image }} class="avatar rounded-circle">
                            </div>
                            <div class="col ml-n2">
                                <div class="progress-wrapper">
                                    <span class="progress-percentage"><small class="font-weight-bold">{{__('Completed:')}} </small>{{ $project->project_progress()['percentage'] }}</span>
                                    <div class="progress progress-xs mt-2">
                                        <div class="progress-bar bg-{{ $project->project_progress()['color'] }}" role="progressbar" aria-valuenow="{{ $project->project_progress()['percentage'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $project->project_progress()['percentage'] }};"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-sm mb-0">
                        {{ $project->descriptions }}
                    </p>
                </div>
                <div class="card-footer py-0 px-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <small>{{__('Start date')}}:</small>
                                    <div class="h6 mb-0">{{ \App\Models\Utility::getDateFormated($project->start_date) }}</div>
                                </div>
                                <div class="col-6">
                                    <small>{{__('End date')}}:</small>
                                    <div class="h6 mb-0 {{ (strtotime($project->end_date) < time()) ? 'text-danger' : '' }}">{{ \App\Models\Utility::getDateFormated($project->end_date) }}</div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{--Users--}}
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="mb-0">{{__('Members')}}</h6>
                        </div>
                        @if(\Auth::user()->checkProject($project->id) == 'Owner')
                            <div class="col-auto">
                                <div class="actions">
                                    <a href="#" class="action-item" data-url="{{ route('invite.project.member.view', $project->id) }}" data-ajax-popup="true" data-size="lg" data-title="{{__('Add Member')}}">
                                        <i class="fas fa-plus"></i>
                                        <span class="d-sm-inline-block">{{__('Add')}}</span>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="table-responsive">
                    {{-- <div class="scrollbar-inner"> --}}
                        <div class="card-wrapper  p-3   table_scroll"  style="overflow: auto; ">
                            <table class="table align-items-center">
                                <tbody class="list" id="project_users">
                                </tbody>
                            </table>
                        </div>
                    {{-- </div> --}}
                </div>
            </div>
        </div>

        {{--Milestone--}}
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">{{__('Milestones')}} ({{count($project->milestones)}})</h6>
                        </div>
                        @if(isset($permissions) && in_array('create milestone',$permissions))
                            <div class="text-right">
                                <a href="#" data-url="{{ route('project.milestone',$project->id) }}" data-ajax-popup="true" data-title="{{__('Create New Milestone')}}" class="action-item">
                                    <i class="fas fa-plus"></i>
                                    <span class="d-sm-inline-block">{{__('Add')}}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                {{-- <div class="min-h-430 mh-430"> --}}
                    <div class="card-wrapper  p-3   table_scroll"  style="overflow: auto; ">
                        <div class="list-group list-group-flush">
                            @if($project->milestones->count() > 0)
                                @foreach($project->milestones as $milestone)
                                    <span class="list-group-item list-group-item-action">
                                    <div class="media align-items-center">
                                        <div class="media-body">
                                            <h6 class="text-sm d-block text-limit mb-0">{{ $milestone->title }}
                                                <span class=" mx-3 badge badge-pill badge-{{\App\Models\Project::$status_color[$milestone->status]}}">{{ __(\App\Models\Project::$status[$milestone->status]) }} </span>{{ $milestone->tasks->count().' '. __('Tasks') }}
                                            </h6>
                                            <span class="d-block text-sm text-muted mt-2">

                                                <div class="progress_wrapper">
                                                       <div class="progress">
                                                          <div class="progress-bar" role="progressbar"  style="width:{{ $milestone->progress }}px;"
                                                             aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                                                       </div>
                                                       <div class="progress_labels">
                                                          <div class="total_progress">
                                                          
                                                             <strong> {{ $milestone->progress }}%</strong>
                                                          </div>
                                                     
                                                       </div>
                                                      </div>

                                            </span>
                                          </div>
                                          

                                        <div class="media-body text-right">


                                            <span class="text-sm ml-3" data-url="{{ route('project.milestone.show',$milestone->id) }}" data-size="lg" data-ajax-popup="true" data-title="{{ $milestone->title }}" data-toggle="tooltip" data-original-title="{{__('View')}}">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                            @if(in_array('edit milestone',$permissions))
                                                <span class="text-sm ml-3" data-url="{{ route('project.milestone.edit',$milestone->id) }}" data-ajax-popup="true" data-title="{{__('Edit Milestone')}}" data-toggle="tooltip" data-original-title="{{__('Edit')}}">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </span>
                                            @endif
                                            @if(in_array('delete milestone',$permissions))
                                                <span class="text-sm ml-3 text-danger" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?')}}|{{__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$milestone->id}}').submit();">
                                                    <i class="fas fa-trash-alt"></i>
                                                </span>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['project.milestone.destroy', $milestone->id],'id'=>'delete-form-'.$milestone->id]) !!}
                                                {!! Form::close() !!}
                                            @endif
                                        </div>
                                    </div>
                                </span>
                                @endforeach
                            @else
                                <div class="py-5">
                                    <h6 class="h6 text-center">{{__('No Milestone Found.')}}</h6>
                                </div>
                            @endif
                        </div>
                    </div>
                {{-- </div> --}}
            </div>
        </div>
    </div>

    <div class="row">
        {{--Attachments--}}
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">{{__('Attachments')}}</h6>
                            @if(\Auth::user()->checkProject($project->id) == 'Owner')
                                <small>{{__('Attachment that uploaded in this project')}}</small>
                            @else
                                <small>{{__('Attachment that uploaded in your assigned tasks')}}</small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- <div class="scrollbar-inner"> --}}
                        <div class="mh-500 min-h-500 card-wrapper  p-3   table_scroll"  style="overflow: auto; ">
                            @if($project->projectAttachments()->count() > 0)
                                @foreach($project->projectAttachments() as $attachment)
                                    <div class="card mb-3 border shadow-none">
                                        <div class="px-3 py-3">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <img src="{{ asset('assets/img/icons/files/'.$attachment->extension.'.png') }}" class="img-fluid" style="width: 40px;">
                                                </div>
                                                <div class="col ml-n2">
                                                    <h6 class="text-sm mb-0">
                                                        <a href="#">{{ $attachment->name }}</a>
                                                    </h6>
                                                    <p class="card-text small text-muted">{{ $attachment->file_size }}</p>
                                                </div>
                                                <div class="col-auto actions"> 
                                                         
                                                    <a href="{{asset(Storage::url('tasks/'.$attachment->file))}}" target="_blank" class="action-item" role="button">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    <a href="{{asset(Storage::url('tasks/'.$attachment->file))}}" download class="action-item" role="button">
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
                    {{-- </div> --}}
                </div>
            </div>
        </div>

        {{--Activity Log--}}
        @if(isset($permissions) && in_array('show activity',$permissions))
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{__('Activity Log')}}</h6>
                                @if(\Auth::user()->checkProject($project->id) == 'Owner')
                                    <small>{{__('Activity Log of this project')}}</small>
                                @else
                                    <small>{{__('Activity Log of your assigned tasks')}}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- <div class="scrollbar-inner"> --}}
                            <div class="mh-500 min-h-500 card-wrapper  p-3   table_scroll"  style="overflow: auto; ">
                                <div class="timeline timeline-one-side" data-timeline-content="axis" data-timeline-axis-style="dashed">
                                    @foreach($project->activities as $activity)
                                        <div class="timeline-block">
                                            <span class="timeline-step timeline-step-sm bg-dark border-dark text-white">
                                                <i class="fas {{$activity->logIcon($activity->log_type)}}"></i>
                                            </span>
                                            <div class="timeline-content">
                                                <span class="text-dark text-sm">{{ __($activity->log_type) }}</span>
                                                <a class="d-block h6 text-sm mb-0">{!! $activity->getRemark() !!}</a>
                                                <small><i class="fas fa-clock mr-1"></i>{{$activity->created_at->diffForHumans()}}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        {{-- </div> --}}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            loadProjectUser();

            // project invite modal
            var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

            $(document).on('click', '.check-invite-members', function (e) {
                var ele = $(this);
                var emailele = $('#invite_email');
                var project_id = $('input[name="project_id"]').val();
                var email = emailele.val();
                var role = $('#usr_role').val();

                // Email Field Validation
                $('.email-error-message').remove();
                if (email == '') {
                    emailele.focus().after('<small class="email-error-message text-danger">{{ __("This field is required.") }}</small>');
                    return false;
                }

                if (!emailReg.test(email)) {
                    emailele.focus().after('<small class="email-error-message text-danger">{{ __("Please enter valid email address.") }}</small>');
                    return false;
                } else {
                    $('.invite_usr').addClass('d-none');

                    $.ajax({
                        url: '{{ route('user.exists') }}',
                        dataType: 'json',
                        data: {
                            'project_id': project_id,
                            'email': email,
                            'role': role
                        },
                        success: function (data) {
                            if (data.code == '202') {
                                $('#commonModal').modal('hide');
                                show_toastr(data.status, data.success, 'success');
                            } else if (data.code == '200') {
                                $('#commonModal').modal('hide');
                                show_toastr(data.status, data.success, 'success');
                                location.reload();
                            } else if (data.code == '404') {
                                $('.invite_user_div').removeClass('d-none');
                                $('.invite-warning').text(data.error).show();
                                $('#invite_email').prop('readonly', true);
                            }
                            ele.removeClass('check-invite-members').addClass('invite-members');
                        }
                    });
                }
            });

            $(document).on('click', '.invite-members', function () {
                var project_id = $('input[name="project_id"]').val();
                var useremail = $('#invite_email').val();
                var username = $('#username').val();
                var userpassword = $('#userpassword').val();
                var role = $('#usr_role').val();

                $('.username-error-message').remove();
                if (username == '') {
                    $('#username').focus().after('<small class="username-error-message text-danger">{{ __("This field is required.") }}</small>');
                    return false;
                }

                $('.userpassword-error-message').remove();
                if (userpassword == '') {
                    $('#userpassword').focus().after('<small class="userpassword-error-message text-danger">{{ __("This field is required.") }}</small>');
                    return false;
                }

                $('.email-error-message').remove();
                if (useremail == '') {
                    $('#invite_email').focus().after('<small class="email-error-message text-danger">{{ __("This field is required.") }}</small>');
                    return false;
                }

                if (!emailReg.test(useremail)) {
                    $('#invite_email').focus().after('<small class="email-error-message text-danger">{{ __("Please enter valid email address.") }}</small>');
                    return false;
                } else {
                    $.ajax({
                        url: '{{ route('invite.project.user.member') }}',
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            'project_id': project_id,
                            'useremail': useremail,
                            'username': username,
                            'userpassword': userpassword,
                            'role': role,
                        },
                        success: function (data) {
                            if (data.code == '200') {
                                $('#commonModal').modal('hide');
                                show_toastr(data.status, data.success, 'success')
                                if ($('#project_users').length > 0) {
                                    loadProjectUser();
                                } else {
                                    ajaxFilterProjectView('created_at-desc', $('#project_keyword').val());
                                }
                            } else if (data.code == '404') {
                                show_toastr(data.status, data.error, 'error')
                            }
                        }
                    });
                }
            });

            $(document).on('click', '.invite-btn', function () {
                var current = $(this);
                var id = current.attr('data-id');
                var project_id = $('input[name="project_id"]').val();
                var role = $('#usr_role').val();

                $.ajax({
                    url: '{{ route('user.exists') }}',
                    dataType: 'json',
                    data: {
                        'project_id': project_id,
                        'id': id,
                        'role': role
                    },
                    success: function (data) {
                        if (data.code == '200') {
                            current.html('Invited');
                            current.html('Invited');
                            current.removeClass('btn-secondary');
                            current.addClass('btn-primary');

                            show_toastr(data.status, data.success, 'success');

                            if ($('#project_users').length > 0) {
                                loadProjectUser();
                            } else {
                                ajaxFilterProjectView('created_at-desc', $('#project_keyword').val());
                            }
                        } else if (data.code == '202') {
                            show_toastr(data.status, data.success, 'success');
                        } else if (data.code == '404') {
                            show_toastr(data.status, data.error, 'error');
                        }
                    }
                });
            });

            $(document).on('click', '.user_role', function () {
                $('#usr_role').val($(this).attr('data-val'));
            })
        });

        function loadProjectUser() {
            var mainEle = $('#project_users');
            var project_id = '{{$project->id}}';

            $.ajax({
                url: '{{ route('project.user') }}',
                data: {project_id: project_id},
                beforeSend: function () {
                    $('#project_users').html('<tr><th colspan="2" class="h6 text-center pt-5">{{__('Loading...')}}</th></tr>');
                },
                success: function (data) {
                    mainEle.html(data.html);
                    $('[id^=fire-modal]').remove();
                    loadConfirm();
                }
            });
        }
    </script>
@endpush
