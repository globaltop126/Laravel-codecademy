
@extends('layouts.admin')

@section('title')
    {{__('Zoom Meeting')}}
@endsection

@section('action-button')
@if(\Auth::user()->type == 'owner' )
    <a href="#" class="btn btn-sm btn-white btn-icon-only rounded-circle ml-2" data-url="{{ route('zoommeeting.create') }}" data-ajax-popup="true" data-size="lg" data-title="{{__('Create Meeting')}}">
        <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
    </a>
@endif
    <a href="{{route('zoommeeting.calendar')}}" class="btn btn-sm btn-white rounded-pill ml-2">
        <span class="btn-inner--icon">{{ __('Calendar') }}</span>
    </a>
@endsection

@section('content')
<div class="card">
    <div class="table-responsive" >
        <table class="table align-items-center" id="myTable">
            <thead>
            <tr>
                <th> {{ __('TITLE') }} </th>
                <th> {{ __('PROJECT') }}  </th>
                @if(\Auth::user()->type == 'owner' )
                <th> {{ __('CLIENT') }}  </th>
                <th> {{ __('EMPLOYEE') }}  </th>
                @endif
                <th> {{ __('MEETING TIME') }} </th>
                <th> {{ __('DURATION') }} </th>
                <th> {{ __('JOIN URL') }} </th>
                <th> {{ __('STATUS') }} </th>
                @if(\Auth::user()->type == 'owner')
                    <th class="text-right"> {{__('Action')}}</th>
                @endif
            </tr>
            </thead>
            <tbody>
                @forelse ($meetings as $item)
                <tr>
                <td>{{$item->title}}</td>
                <td>{{ !empty($item->projectName)?$item->projectName->name:'' }}</td>
                @if(\Auth::user()->type == 'owner' )
                <td> 
                    @if($item->client_id != '0')
                        @foreach($item->clients($item->client_id) as $projectClient)
                            <a href="#" class="avatar rounded-circle avatar-sm">
                                <img alt="" @if(!empty($users->avatar)) src="{{$profile.'/'.$projectClient->avatar}}" @else  avatar="{{(!empty($projectClient)?$projectClient->name:'')}}" @endif data-original-title="{{(!empty($projectClient)?$projectClient->name:'')}}" data-toggle="tooltip" data-original-title="{{(!empty($projectClient)?$projectClient->name:'')}}" class="">
                            </a>
                        @endforeach
                    @else
                         -
                    @endif
                   
                </td>
                
                <td>
                    @foreach($item->users($item->user_id) as $projectUser)
                        <a href="#" class="avatar rounded-circle avatar-sm">
                            <img alt="" @if(!empty($users->avatar)) src="{{$profile.'/'.$projectUser->avatar}}" @else  avatar="{{(!empty($projectUser)?$projectUser->name:'')}}" @endif data-original-title="{{(!empty($projectUser)?$projectUser->name:'')}}" data-toggle="tooltip" data-original-title="{{(!empty($projectUser)?$projectUser->name:'')}}" class="">
                        </a>
                    @endforeach
                </td>
                @endif
                <td>{{$item->start_date}}</td>
                <td>{{$item->duration}} {{__("Minutes")}}</td>
               
                <td>
                    @if($item->created_by == \Auth::user()->id && $item->checkDateTime())
                    <a href="{{$item->start_url}}" target="_blank"> {{__('Start meeting')}} <i class="fas fa-external-link-square-alt "></i></a>
                    @elseif($item->checkDateTime())
                        <a href="{{$item->join_url}}" target="_blank"> {{__('Join meeting')}} <i class="fas fa-external-link-square-alt "></i></a>
                    @else
                        -
                    @endif

                </td>
                <td>
                    @if($item->checkDateTime())
                        @if($item->status == 'waiting')
                            <span class="badge badge-info">{{ucfirst($item->status)}}</span>
                        @else
                            <span class="badge badge-success">{{ucfirst($item->status)}}</span>
                        @endif
                    @else
                        <span class="badge badge-danger">{{__("End")}}</span>
                    @endif
                </td>
                @if(\Auth::user()->type == 'owner')
                <td class="text-right">
                
                    <a href="#!" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?"
                     data-confirm-yes="document.getElementById('delete-form-{{$item->id}}').submit();">
                        <i class="fas fa-trash"></i>
                    </a>
                    {!! Form::open(['method' => 'DELETE', 'route' => ['zoommeeting.destroy', $item->id],'id'=>'delete-form-'.$item->id]) !!}
                        {!! Form::close() !!}
                </td>
                @endif
            </tr>
                @empty
                    
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection