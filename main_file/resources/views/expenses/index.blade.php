@extends('layouts.admin')

@section('title')
    {{$project->name.__("'s Expenses")}}
@endsection

@section('role'){{$expense_cnt}}@endsection

@php
    $permissions = \Auth::user()->getPermission($project->id);
@endphp

@section('action-button')
    <a href="{{ route('projects.show',$project->id) }}" class="btn btn-sm btn-white rounded-circle btn-icon-only ml-0">
        <span class="btn-inner--icon"><i class="fas fa-arrow-left"></i></span>
    </a>
    @if(isset($permissions) && in_array('create expense',$permissions))
        <a href="#" class="btn btn-sm btn-white btn-icon-only rounded-circle ml-0" data-url="{{ route('projects.expenses.create',$project->id) }}" data-ajax-popup="true" data-size="lg" data-title="{{__('Create Expense')}}">
            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
        </a>
    @endif
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="table-responsive">
                    <table class="table align-items-center">
                        <thead>
                        <tr>
                            <th scope="col">{{__('Attachment')}}</th>
                            <th scope="col">{{__('Name')}}</th>
                            <th scope="col">{{__('Date')}}</th>
                            <th scope="col">{{__('Amount')}}</th>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody class="list">
                        @if(isset($permissions) && in_array('show expense',$permissions))
                            @if(isset($project->expense) && !empty($project->expense) && count($project->expense) > 0)
                                @foreach($project->expense as $expense)
                                    <tr>
                                        <th scope="row">
                                            @if(!empty($expense->attachment))
                                                <a href="{{ asset(Storage::url($expense->attachment)) }}" class="btn btn-sm btn-secondary btn-icon rounded-pill" download>
                                                    <span class="btn-inner--icon"><i class="fas fa-download"></i></span>
                                                </a>
                                            @else
                                                <a href="#" class="btn btn-sm btn-secondary btn-icon rounded-pill">
                                                    <span class="btn-inner--icon"><i class="fas fa-times-circle"></i></span>
                                                </a>
                                            @endif
                                        </th>
                                        <td>
                                            <span class="h6 text-sm font-weight-bold mb-0">{{ $expense->name }}</span>
                                            @if(!empty($expense->task))<span class="d-block text-sm text-muted">{{ $expense->task->name }}</span>@endif
                                        </td>
                                        <td>{{ (!empty($expense->date)) ? \App\Models\Utility::getDateFormated($expense->date) : '-' }}</td>
                                        <td>{{ \App\Models\Utility::projectCurrencyFormat($project->id,$expense->amount) }}</td>
                                        <td class="text-right w-15">
                                            <div class="actions">
                                                <a href="#" class="action-item px-2" data-url="{{ route('projects.expenses.edit',[$project->id,$expense->id]) }}" data-ajax-popup="true" data-size="lg" data-title="{{__('Edit ').$expense->name}}" data-toggle="tooltip" data-original-title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="#" class="action-item text-danger px-2" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?')}}|{{__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-expense-{{$expense->id}}').submit();">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['projects.expenses.destroy',$expense->id],'id'=>'delete-expense-'.$expense->id]) !!}
                                            {!! Form::close() !!}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <th scope="col" colspan="5"><h6 class="text-center">{{__('No Expense Found.')}}</h6></th>
                                </tr>
                            @endif
                        @else
                            <tr>
                                <th scope="col" colspan="5"><h6 class="text-center">{{__('Permission Denied.')}}</h6></th>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
