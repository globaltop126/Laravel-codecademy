@extends('layouts.admin')

@section('title')
    {{__('Manually Plan Request')}}
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" width="100%">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }} </th>
                                    <th>{{ __('Plan Name') }} </th>
                                    <th>{{ __('Users') }} </th>
                                    <th>{{ __('Projects') }} </th>
                                    <th>{{ __('Duration') }} </th>
                                    <th>{{ __('Date') }} </th>
                                    <th>{{ __('Action') }} </th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                            @if($plan_requests->count() > 0)
                                @foreach($plan_requests as $prequest)
                                    <tr>
                                        <td>
                                            <div class="font-style font-weight-bold">{{ $prequest->user->name }}</div>
                                        </td>
                                        <td>
                                            <div class="font-style font-weight-bold">{{ $prequest->plan->name }}</div>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold">{{ $prequest->plan->max_users }}</div>
                                            <div>{{__('Users')}}</div>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold">{{ $prequest->plan->max_projects }}</div>
                                            <div>{{__('Projects')}}</div>
                                        </td>
                                        <td>
                                            <div class="font-style font-weight-bold">{{ ($prequest->duration == 'monthly') ? __('One Month') : __('One Year') }}</div>
                                        </td>
                                        <td>{{ \App\Models\Utility::getDateFormated($prequest->created_at,true) }}</td>
                                        <td>
                                            <div>
                                                <a href="{{route('response.request',[$prequest->id,1])}}" class="btn btn-success btn-xs">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                                <a href="{{route('response.request',[$prequest->id,0])}}" class="btn btn-danger btn-xs">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <th scope="col" colspan="7"><h6 class="text-center">{{__('No Manually Plan Request Found.')}}</h6></th>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
