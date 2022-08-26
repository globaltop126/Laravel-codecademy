@extends('layouts.admin')

@section('title')
    {{__('Email Templates')}}
@endsection

@push('theme-script')
    <script src="{{ asset('assets/libs/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
@endpush

{{-- @section('action-button')
    <a href="#" class="btn btn-sm btn-white btn-icon-only rounded-circle ml-2" data-ajax-popup="true" data-title="{{__('Create New Email Template')}}" data-url="{{route('email_template.create')}}">
        <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
    </a>
@endsection --}}

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="thead-light">
                        <tr>
                            <th> {{__('Name')}}</th>
                            <th class="text-right"> {{__('Action')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($EmailTemplates->count() > 0 )
                            @foreach ($EmailTemplates as $EmailTemplate)
                                <tr>
                                    <td>{{ $EmailTemplate->name }}</td>
                                    <td class="action text-right">
                                        <a href="{{ route('manage.email.language',[$EmailTemplate->id,\Auth::user()->lang]) }}" class="action-item px-2" data-toggle="tooltip" data-original-title="{{__('View')}}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <th scope="col" colspan="2"><h6 class="text-center">{{__('No Email Template found')}}</h6></th>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
