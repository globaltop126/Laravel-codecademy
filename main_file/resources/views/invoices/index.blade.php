@extends('layouts.admin')

@section('title')
    {{__('Invoices')}}
@endsection

@section('action-button')
    <a href="#" class="btn btn-sm btn-white btn-icon-only rounded-circle ml-2" data-url="{{ route('invoices.create') }}" data-ajax-popup="true" data-size="md" data-title="{{__('Create Invoice')}}">
        <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
    </a>
    <a href="#" class="btn btn-sm btn-white btn-icon-only rounded-circle" data-url="{{ route('invoice.file.import') }}" data-ajax-popup="true" data-title="{{__('Import Members CSV file')}}" data-toggle="tooltip">
        <i class="fa fa-file-csv"></i> 
    </a>
@endsection

@section('content')
    <ul class="nav nav-dark nav-tabs nav-overflow" role="tablist">
        <li class="nav-item">
            <a href="#send" id="send-tab" class="nav-link active" data-toggle="tab" role="tab" aria-selected="true">
                {{__('Send')}}
            </a>
        </li>
        <li class="nav-item">
            <a href="#receive" id="receive-tab" class="nav-link" data-toggle="tab" role="tab" aria-selected="true">
                {{__('Received')}}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade show active" id="send" role="tabpanel" aria-labelledby="send-tab">
            <div class="row">
                <div class="col-12 pb-4">
                    <span class="text-white font-weight-700">{{__('Note :')}}</span>
                    <span class="text-white">{{__('You have sent this invoice to your client.')}}</span>
                </div>
                @if($send_invoice->count() > 0)
                    @foreach($send_invoice as $invoice)
                        <div class="col-lg-4">
                            <div class="card hover-shadow-lg">
                                <div class="card-header border-0">
                                    <div class="row align-items-center">
                                        <div class="col-10">
                                            <h6 class="mb-0"><a href="{{route('invoices.show',$invoice->id)}}">{{ $invoice->project->name }}</a></h6>
                                        </div>
                                        <div class="col-2 text-right">
                                            <div class="actions">
                                                <div class="dropdown">
                                                    <a href="#" class="action-item" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></a>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a href="{{route('invoices.show',$invoice->id)}}" class="dropdown-item">
                                                            {{__('Show')}}
                                                        </a>
                                                        <a href="#" class="dropdown-item" data-url="{{ route('invoices.edit',[$invoice->id]) }}" data-ajax-popup="true" data-size="md" data-title="{{__('Edit ').\App\Models\Utility::invoiceNumberFormat($invoice->invoice_id)}}">
                                                            {{__('Edit')}}
                                                        </a>
                                                        <a href="#" class="dropdown-item" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="document.getElementById('delete-form-{{$invoice->id}}').submit();">
                                                            {{__('Delete')}}
                                                        </a>
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['invoices.destroy', $invoice->id],'id'=>'delete-form-'.$invoice->id]) !!}
                                                        {!! Form::close() !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="p-3 border border-dashed">
                                        <span class="text-sm text-muted font-weight-600">{{\App\Models\Utility::invoiceNumberFormat($invoice->invoice_id)}}</span>
                                        <div class="row align-items-center mt-3">
                                            <div class="col-6">
                                                <h6 class="mb-0">{{ \App\Models\Utility::projectCurrencyFormat($invoice->project_id,($invoice->getSubTotal()+$invoice->getTax()),true) }}</h6>
                                                <span class="text-sm text-muted">{{__('Amount')}}</span>
                                            </div>
                                            <div class="col-6">
                                                <h6 class="mb-0">{{ \App\Models\Utility::getDateFormated($invoice->due_date) }}</h6>
                                                <span class="text-sm text-muted">{{__('Due Date')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="media mt-4 align-items-center">
                                        <img {{$invoice->client->img_avatar}} class="avatar rounded-circle avatar-sm">
                                        <div class="media-body pl-3">
                                            <div class="text-sm my-0">{{$invoice->client->name}} <span class="text-primary font-weight-600">{{'@'.__('client')}}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-center my-3">{{__('No Invoice Found.')}}</h6>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="tab-pane fade show" id="receive" role="tabpanel" aria-labelledby="receive-tab">
            <div class="row">
                <div class="col-12 pb-4">
                    <span class="text-white font-weight-700">{{__('Note :')}}</span>
                    <span class="text-white">{{__('You have got this invoice from user.')}}</span>
                </div>
                @if($receive_invoice->count() > 0)
                    @foreach($receive_invoice as $invoice)
                        <div class="col-lg-4">
                            <div class="card hover-shadow-lg">
                                <div class="card-header border-0">
                                    <div class="row align-items-center">
                                        <div class="col-10">
                                            <h6 class="mb-0"><a href="{{route('invoices.show',$invoice->id)}}">{{ $invoice->project->name }}</a></h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="p-3 border border-dashed">
                                        <span class="text-sm text-muted font-weight-600">{{\App\Models\Utility::invoiceNumberFormat($invoice->invoice_id)}}</span>
                                        <div class="row align-items-center mt-3">
                                            <div class="col-6">
                                                <h6 class="mb-0">{{ \App\Models\Utility::projectCurrencyFormat($invoice->project_id,($invoice->getSubTotal()+$invoice->getTax()),true) }}</h6>
                                                <span class="text-sm text-muted">{{__('Amount')}}</span>
                                            </div>
                                            <div class="col-6">
                                                <h6 class="mb-0">{{ \App\Models\Utility::getDateFormated($invoice->due_date) }}</h6>
                                                <span class="text-sm text-muted">{{__('Due Date')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="media mt-4 align-items-center">
                                        <img {{$invoice->user->img_avatar}} class="avatar rounded-circle avatar-sm">
                                        <div class="media-body pl-3">
                                            <div class="text-sm my-0">{{$invoice->user->name}} <span class="text-primary font-weight-600">{{'@'.__('user')}}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-center my-3">{{__('No Invoice Found.')}}</h6>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function fillClient(project_id, selected = 0) {
            $.ajax({
                url: '{{route('project.client.json')}}',
                data: {project_id: project_id},
                type: 'POST',
                success: function (data) {
                    $('#client_id').html('');

                    if (data != '') {
                        $('#no_client').addClass('d-none');
                        $.each(data, function (key, data) {
                            var selected = '';
                            if (key == selected) {
                                selected = 'selected';
                            }
                            $("#client_id").append('<option value="' + key + '" ' + selected + '>' + data + '</option>');
                        });
                    } else {
                        $('#no_client').removeClass('d-none');
                    }
                }
            })
        }
    </script>
@endpush
