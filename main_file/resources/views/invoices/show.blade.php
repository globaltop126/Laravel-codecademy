@extends('layouts.admin')

@section('title')
    {{__('Invoice ').\App\Models\Utility::invoiceNumberFormat($invoice->invoice_id)}}
@endsection

@push('css')
    <style>
        #card-element {
            border: 1px solid #e4e6fc;
            border-radius: 5px;
            padding: 10px;
        }
    </style>
@endpush

@section('action-button')
    @if(Auth::user()->id == $invoice->created_by)
    <a href="#" class="btn btn-sm btn-white btn-icon-only rounded-circle ml-0 cp_link" data-link="{{route('pay.invoice',\Illuminate\Support\Facades\Crypt::encrypt($invoice->id))}}" data-toggle="tooltip" data-original-title="{{__('Click to copy invoice link')}}">
        <span class="btn-inner--icon"><i class="fas fa-file"></i></span>
    </a>
        <a href="#" class="btn btn-sm btn-white btn-icon-only rounded-circle ml-0" data-url="{{ route('invoices.products.add',$invoice->id) }}" data-ajax-popup="true" data-size="md" data-title="{{__('Add Item')}}" data-toggle="tooltip" data-original-title="{{__('Add Item')}}">
            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
        </a>
        <a href="#" class="btn btn-sm btn-white btn-icon-only rounded-circle ml-0" data-size="lg" data-url="{{ route('invoices.payments.create',$invoice->id) }}" data-ajax-popup="true" data-title="{{__('Add Payment')}}" data-toggle="tooltip" data-original-title="{{__('Add Payment')}}">
            <span class="btn-inner--icon"><i class="fas fa-shopping-cart"></i></span>
        </a>
        <a href="{{ route('invoice.sent',$invoice->id) }}" class="btn btn-sm btn-white btn-icon-only rounded-circle ml-0" data-toggle="tooltip" data-original-title="{{__('Send Invoice Mail')}}">
            <span class="btn-inner--icon"><i class="fas fa-reply"></i></span>
        </a>
        <a href="{{ route('invoice.payment.reminder',$invoice->id) }}" class="btn btn-sm btn-white btn-icon-only rounded-circle ml-0" data-title="{{__('Payment Reminder')}}" data-toggle="tooltip" data-original-title="{{__('Payment Reminder')}}">
            <span class="btn-inner--icon"><i class="fas fa-money-check"></i></span>
        </a>
        <a href="#" class="btn btn-sm btn-white btn-icon-only rounded-circle ml-0" data-url="{{ route('invoices.edit',$invoice->id) }}" data-ajax-popup="true" data-size="md" data-title="{{__('Edit ').\App\Models\Utility::invoiceNumberFormat($invoice->invoice_id)}}" data-toggle="tooltip" data-original-title="{{__('Edit')}}">
            <span class="btn-inner--icon"><i class="fas fa-edit"></i></span>
        </a>
    @endif
    @if($invoice->client_id == Auth::user()->id)
        @if($invoice->getDue() > 0)
            @if($paymentSetting['enable_stripe'] == 'on' || $paymentSetting['enable_paypal'] == 'on' || $paymentSetting['is_paystack_enabled'] == 'on' || $paymentSetting['is_flutterwave_enabled'] == 'on' || $paymentSetting['is_razorpay_enabled'] == 'on' || $paymentSetting['is_mercado_enabled'] == 'on' || $paymentSetting['is_paytm_enabled'] == 'on' || $paymentSetting['is_mollie_enabled'] == 'on' || $paymentSetting['is_skrill_enabled'] == 'on' || $paymentSetting['is_coingate_enabled'] == 'on')
                <a href="#" class="btn btn-sm btn-white btn-icon-only rounded-circle ml-0" data-toggle="modal" data-target="#paymentModal" data-original-title="{{__('Add Payment')}}" title="{{__('Add Payment')}}">
                    <span class="btn-inner--icon"><i class="fas fa-shopping-cart"></i></span>
                </a>
            @endif
        @endif
        <a href="#" data-url="{{ route('invoice.custom.send',$invoice->id) }}" data-ajax-popup="true" data-title="{{__('Send Invoice')}}" data-toggle="tooltip" data-original-title="{{__('Send Invoice')}}" class="btn btn-sm btn-white btn-icon-only rounded-circle ml-0">
            <span class="btn-inner--icon"><i class="fas fa-share-square"></i></span>
        </a>
    @endif

    <a href="{{ route('get.invoice',Crypt::encrypt($invoice->id)) }}" class="btn btn-sm btn-white btn-icon-only rounded-circle ml-2" data-toggle="tooltip" data-original-title="{{__('Print Invoice')}}" target="_blanks">
        <span><i class="fa fa-print"></i></span>
    </a>
    <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-white rounded-circle btn-icon-only ml-0" data-toggle="tooltip" data-original-title="{{__('Back')}}">
        <span class="btn-inner--icon"><i class="fas fa-arrow-left"></i></span>
    </a>
@endsection

@section('content')
    <div class="card card-body p-md-5">
        <div class="row align-items-center mb-5">
            <div class="col-sm-6 mb-3 mb-sm-0">
                @if(Auth::user()->mode == 'dark')
                    @if($invoice->client_id == Auth::user()->id)
                        <img src="{{ asset(Storage::url($right_address['dark_logo'])) }}" alt="" height="40">
                    @else
                        <img src="{{ asset(Storage::url($left_address['dark_logo'])) }}" alt="" height="40">
                    @endif
                @else
                    @if($invoice->client_id == Auth::user()->id)
                        <img src="{{ asset(Storage::url($right_address['light_logo'])) }}" alt="" height="40">
                    @else
                        <img src="{{ asset(Storage::url($left_address['light_logo'])) }}" alt="" height="40">
                    @endif
                @endif

            </div>

            <div>
                <div class="col-lg-6 col-md-6" style="right: -391px; padding-top: 10px;">
                    <span class="badge badge-pill badge-{{\App\Models\Invoice::$status_color[$invoice->status]}} ml-3">
                        {{__(\App\Models\Invoice::$status[$invoice->status])}}</span>
                </div>
                
                <div class="col-lg-6 col-md-6 text-align-right " style="right: -356px; padding-top: 10px;" style="position: relative">
                {!! DNS2D::getBarcodeHTML(route('pay.invoice',\Illuminate\Support\Facades\Crypt::encrypt($invoice->id)), "QRCODE",2,2) !!}
            </div>
            

            
            </div>

        </div>
        <div class="row mb-3">
            <div class="col-lg-6 col-md-6">
                <h6 class="">{{__('From :')}}</h6>
                <p class="text-sm font-weight-700 mb-0">
                    {{ Auth::user()->name }}
                </p>
                <span class="text-sm">
                    {{ $left_address['address'] }} <br>
                    {{ $left_address['city'] }}
                    @if(isset($left_address['city']) && !empty($left_address['city'])), @endif
                    {{$left_address['state']}}
                    @if(isset($left_address['zipcode']) && !empty($left_address['zipcode']))-@endif {{$left_address['zipcode']}}<br>
                    {{$left_address['country']}} <br>
                    {{$left_address['telephone']}}
                </span>
            </div>
            <div class="col-lg-6 col-md-6 text-right">
                <h6 class="">{{__('To :')}}</h6>
                <p class="text-sm font-weight-700 mb-0">
                    @if($invoice->client_id == Auth::user()->id)
                        {{ $invoice->user->name }}
                    @else
                        {{ $invoice->client->name }}
                    @endif
                </p>
                <span class="text-sm">
                    {{ $right_address['address'] }} <br>
                    {{ $right_address['city'] }}
                    @if(isset($right_address['city']) && !empty($right_address['city'])), @endif
                    {{$right_address['state']}}
                    @if(isset($right_address['zipcode']) && !empty($right_address['zipcode']))-@endif {{$right_address['zipcode']}}<br>
                    {{$right_address['country']}} <br>
                    {{$right_address['telephone']}}
                    
                </span>
            </div>
        </div>
        <div class="row pt-3">
            <div class="col-md-4 text-sm">
                <strong>{{__('Project')}}</strong><br>
                {{$invoice->project->name}}<br>
            </div>
            <div class="col-md-4 text-sm text-center">
                <strong>{{__('Due Date')}}</strong><br>
                {{\App\Models\Utility::getDateFormated($invoice->due_date)}}<br>
            </div>
            <div class="col-md-4 text-sm text-right">
                <strong>{{__('Due Amount')}}</strong><br>
                {{\App\Models\Utility::projectCurrencyFormat($invoice->project_id,$invoice->getDue(),true)}}<br>
            </div>
            <div class="col-12">
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-6 text-left pb-3">
                <h5>{{__('Item List')}}</h5>
            </div>
            @if(Auth::user()->id == $invoice->created_by)
                <div class="col-6 text-right pb-3">
                    <button type="button" class="btn btn-warning btn-xs btn-icon" data-url="{{ route('invoices.products.add',$invoice->id) }}" data-ajax-popup="true" data-size="md" data-title="{{__('Add Item')}}">
                    <span class="btn-inner--icon">
                      <i class="fas fa-plus"></i>
                    </span>
                        <span class="btn-inner--text">{{__('Add Item')}}</span>
                    </button>
                </div>
            @endif
            <div class="col-12">
                <!-- Table -->
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                        <tr>
                            <th class="px-0 bg-transparent border-top-0">{{__('Item')}}</th>
                            <th class="px-0 bg-transparent border-top-0">{{__('Price')}}</th>
                            <th class="px-0 bg-transparent border-top-0">{{__('Tax')}}</th>
                            @if(Auth::user()->id == $invoice->created_by)
                                <th class="px-0 bg-transparent border-top-0 text-right">{{__('Action')}}</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($invoice->items as $item)
                            <tr>
                                <td class="px-0">
                                    <span class="h6 text-sm">{{ $item->item }}</span>
                                </td>
                                {{-- {{ dd($invoice->project_id,$item->price) }} --}}
                              
                                <td class="px-0">
                                    {{ number_format($item->price) }}
                                </td>
                                {{-- {{ dd($item->tax()) }} --}}
                                <td class="px-0">{{ number_format($item->tax()) }}</td>
                                @if(Auth::user()->id == $invoice->created_by)
                                    <td class="px-0 text-right">
                                        <a href="#" class="table-action table-action-delete text-danger" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="document.getElementById('delete-form-{{$item->id}}').submit();">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['invoices.products.delete', $invoice->id,$item->id],'id'=>'delete-form-'.$item->id]) !!}
                                        {!! Form::close() !!}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card my-5 bg-secondary">
                    <div class="card-body">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-6 order-md-2 mb-4 mb-md-0">
                                <div class="d-flex align-items-center justify-content-md-end">
                                    <span class="d-inline-block mr-3 mb-0">{{__('Total value:')}}</span>
                                    
                                    <span class="h4 mb-0">{{ number_format($invoice->getSubTotal()+$invoice->getTax()) }}</span>
                                </div>
                            </div>
                            <div class="col-md-3 order-md-1">
                                <div class="text text-sm">
                                    <span class="font-weight-bold">{{__('Subtotal')}}</span><br>
                                    <span class="text-dark">{{ number_format($invoice->getSubTotal()) }}</span>
                                </div>
                            </div>
                            <div class="col-md-3 order-md-1">
                                <div class="text text-sm">
                                    <span class="font-weight-bold">{{(!empty($invoice->tax)?$invoice->tax->name:'Tax')}} ({{(!empty($invoice->tax)?$invoice->tax->rate:'0')}} %)</span><br>
                                    <span class="text-dark">{{ number_format($invoice->getTax()) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <h5 class="pb-3">{{__('Payment History')}}</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead class="thead-light">
                        <tr>
                            <th>{{__('Transaction ID')}}</th>
                            <th>{{__('Payment Date')}}</th>
                            <th>{{__('Payment Type')}}</th>
                            <th>{{__('Note')}}</th>
                            <th>{{__('Amount')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($invoice->payments->count())
                            @foreach($invoice->payments as $payment)
                                <tr>
                                    <td>{{sprintf("%05d", $payment->transaction_id)}}</td>
                                    <td>{{ \App\Models\Utility::getDateFormated($payment->date) }}</td>
                                    <td>{{$payment->payment_type}}</td>
                                    <td>{{(!empty($payment->notes)) ? $payment->notes : '-'}}</td>
                                    <td>{{\App\Models\Utility::projectCurrencyFormat($invoice->project_id,$payment->amount,true)}}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <th scope="col" colspan="5"><h6 class="text-center">{{__('No Record Found.')}}</h6></th>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($invoice->client_id == Auth::user()->id)
        @if($invoice->getDue() > 0)
            @if($paymentSetting['enable_stripe'] == 'on' || $paymentSetting['enable_paypal'] == 'on' || $paymentSetting['is_paystack_enabled'] == 'on' || $paymentSetting['is_flutterwave_enabled'] == 'on' || $paymentSetting['is_razorpay_enabled'] == 'on' || $paymentSetting['is_mercado_enabled'] == 'on' || $paymentSetting['is_paytm_enabled'] == 'on' || $paymentSetting['is_mollie_enabled'] == 'on' || $paymentSetting['is_skrill_enabled'] == 'on' || $paymentSetting['is_coingate_enabled'] == 'on')
                <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="paymentModalLabel">{{ __('Add Payment') }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <ul class="nav nav-pills pb-3" role="tablist">
                                    @if($paymentSetting['enable_stripe'] == 'on')
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" href="#stripe-payment" role="tab" aria-controls="stripe" aria-selected="true">{{ __('Stripe') }}</a>
                                        </li>
                                    @endif
                                    @if($paymentSetting['enable_paypal'] == 'on')
                                        <li class="nav-item">
                                            <a class="nav-link {{ ($paymentSetting['enable_stripe'] == 'off' && $paymentSetting['enable_paypal'] == 'on') ? "active" : "" }}" data-toggle="tab" href="#paypal-payment" role="tab" aria-controls="paypal" aria-selected="false">{{ __('Paypal') }}</a>
                                        </li>
                                    @endif
                                    @if(isset($paymentSetting['is_paystack_enabled']) && $paymentSetting['is_paystack_enabled'] == 'on')
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#paystack-payment" role="tab" aria-controls="paystack" aria-selected="false">{{ __('Paystack') }}</a>
                                        </li>
                                    @endif
                                    @if(isset($paymentSetting['is_flutterwave_enabled']) && $paymentSetting['is_flutterwave_enabled'] == 'on')
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#flutterwave-payment" role="tab" aria-controls="flutterwave" aria-selected="false">{{ __('Flutterwave') }}</a>
                                        </li>
                                    @endif
                                    @if(isset($paymentSetting['is_razorpay_enabled']) && $paymentSetting['is_razorpay_enabled'] == 'on')
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#razorpay-payment" role="tab" aria-controls="razorpay" aria-selected="false">{{ __('Razorpay') }}</a>
                                        </li>
                                    @endif
                                    @if(isset($paymentSetting['is_mercado_enabled']) && $paymentSetting['is_mercado_enabled'] == 'on')
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#mercado-payment" role="tab" aria-controls="mercado" aria-selected="false">{{ __('Mercado Pago') }}</a>
                                        </li>
                                    @endif
                                    @if(isset($paymentSetting['is_paytm_enabled']) && $paymentSetting['is_paytm_enabled'] == 'on')
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#paytm-payment" role="tab" aria-controls="paytm" aria-selected="false">{{ __('Paytm') }}</a>
                                        </li>
                                    @endif
                                    @if(isset($paymentSetting['is_mollie_enabled']) && $paymentSetting['is_mollie_enabled'] == 'on')
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#mollie-payment" role="tab" aria-controls="mollie" aria-selected="false">{{ __('Mollie') }}</a>
                                        </li>
                                    @endif
                                    @if(isset($paymentSetting['is_skrill_enabled']) && $paymentSetting['is_skrill_enabled'] == 'on')
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#skrill-payment" role="tab" aria-controls="skrill" aria-selected="false">{{ __('Skrill') }}</a>
                                        </li>
                                    @endif
                                    @if(isset($paymentSetting['is_coingate_enabled']) && $paymentSetting['is_coingate_enabled'] == 'on')
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#coingate-payment" role="tab" aria-controls="coingate" aria-selected="false">{{ __('CoinGate') }}</a>
                                        </li>
                                    @endif
                                    @if(isset($paymentSetting['is_paymentwall_enabled']) && $paymentSetting['is_paymentwall_enabled'] == 'on')
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#paymentwall-payment" role="tab" aria-controls="paymentwall" aria-selected="false">{{ __('PaymentWall') }}</a>
                                        </li>
                                    @endif
                                </ul>

                                <div class="tab-content">
                                    @if($paymentSetting['enable_stripe'] == 'on')
                                        <div class="tab-pane fade {{ (($paymentSetting['enable_stripe'] == 'on' && $paymentSetting['enable_paypal'] == 'on') || $paymentSetting['enable_stripe'] == 'on') ? "show active" : "" }}" id="stripe-payment" role="tabpanel" aria-labelledby="stripe-payment">
                                            <form method="post" action="{{ route('client.invoice.payment',[$invoice->id]) }}" class="require-validation" id="payment-form">
                                                @csrf
                                                <div class="border p-3 mb-3 rounded">
                                                    <div class="row">
                                                        <div class="col-sm-8">
                                                            <div class="custom-radio">
                                                                <label class="font-16 font-weight-bold">{{__('Credit / Debit Card')}}</label>
                                                            </div>
                                                            <p class="mb-0 pt-1">{{__('Safe money transfer using your bank account. We support Mastercard, Visa, Discover and American express.')}}</p>
                                                        </div>
                                                        <div class="col-sm-4 text-sm-right mt-3 mt-sm-0">
                                                            <img src="{{asset('assets/img/payments/master.png')}}" height="24" alt="master-card-img">
                                                            <img src="{{asset('assets/img/payments/paypal.png')}}" height="24" alt="paypal-card-img">
                                                            <img src="{{asset('assets/img/payments/visa.png')}}" height="24" alt="visa-card-img">
                                                            <img src="{{asset('assets/img/payments/american-express.png')}}" height="24" alt="american-express-card-img">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <hr>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="card-name-on" class="form-control-label">{{__('Name on card')}}</label>
                                                                <input type="text" name="name" id="card-name-on" class="form-control required" placeholder="{{\Auth::user()->name}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div id="card-element">
                                                                <!-- A Stripe Element will be inserted here. -->
                                                            </div>
                                                            <div id="card-errors" role="alert"></div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <br>
                                                            <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span class="input-group-text">{{ $invoice->project->currency }}</span></span>
                                                                <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="error" style="display: none;">
                                                                <div class='alert-danger alert'>{{__('Please correct the errors and try again.')}}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group mt-3 text-sm-right">
                                                    <button class="btn btn-primary btn-sm rounded-pill" type="submit">{{ __('Make Payment') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                    @if($paymentSetting['enable_paypal'] == 'on')
                                        <div class="tab-pane fade {{ ($paymentSetting['enable_stripe'] == 'off' && $paymentSetting['enable_paypal'] == 'on') ? "show active" : "" }}" id="paypal-payment" role="tabpanel" aria-labelledby="paypal-payment">
                                            <form class="w3-container w3-display-middle w3-card-4 " method="POST" id="payment-form" action="{{ route('client.pay.with.paypal', $invoice->id) }}">
                                                @csrf
                                                <div class="border p-3 mb-3 rounded">
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span class="input-group-text">{{ $invoice->project->currency }}</span></span>
                                                                <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group mt-3 text-sm-right">
                                                    <input type="hidden" value="invoice" name="from">
                                                    <input type="hidden" value="{{$invoice->created_by}}" name="invoice_creator">
                                                    <button class="btn btn-primary btn-sm rounded-pill" name="submit" type="submit">{{ __('Make Payment') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                    @if($paymentSetting['is_paystack_enabled'] == 'on')
                                        <div class="tab-pane fade " id="paystack-payment" role="tabpanel" aria-labelledby="paystack-payment">
                                            <form method="post" action="{{route('invoice.pay.with.paystack')}}" class="require-validation" id="paystack-payment-form">
                                                @csrf
                                                <div class="border p-3 mb-3 rounded">
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span class="input-group-text">{{isset($invoice->project->currency)?$invoice->project->currency:'$'}}</span></span>
                                                                <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="" id="amount">
                                                                <input type="hidden" value="invoice" name="from">
                                                                <input type="hidden" value="{{$invoice->created_by}}" name="invoice_creator">
                                                                <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group mt-3 text-sm-right">
                                                    <button class="btn btn-primary btn-sm rounded-pill" type="button" id="pay_with_paystack">{{ __('Make Payment') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                    @if(isset($paymentSetting['is_flutterwave_enabled']) && $paymentSetting['is_flutterwave_enabled'] == 'on')
                                        <div class="tab-pane fade " id="flutterwave-payment" role="tabpanel" aria-labelledby="flutterwave-payment">
                                            <form method="post" action="{{route('invoice.pay.with.flaterwave')}}" class="require-validation" id="flaterwave-payment-form">
                                                @csrf
                                                <div class="border p-3 mb-3 rounded">
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label class="form-control-label" for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span class="input-group-text">{{isset($invoice->project->currency)?$invoice->project->currency:'$'}}</span></span>
                                                                <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="" id="amount">
                                                                <input type="hidden" value="invoice" name="from">
                                                                <input type="hidden" value="{{$invoice->created_by}}" name="invoice_creator">
                                                                <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group mt-3 text-sm-right">
                                                    <button class="btn btn-primary btn-sm rounded-pill" type="button" id="pay_with_flaterwave">{{ __('Make Payment') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                    @if(isset($paymentSetting['is_razorpay_enabled']) && $paymentSetting['is_razorpay_enabled'] == 'on')
                                        <div class="tab-pane fade " id="razorpay-payment" role="tabpanel" aria-labelledby="razorpay-payment">
                                            <form method="post" action="{{route('invoice.pay.with.razorpay')}}" class="require-validation" id="razorpay-payment-form">
                                                @csrf
                                                <div class="border p-3 mb-3 rounded">
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label class="form-control-label" for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span class="input-group-text">{{isset($invoice->project->currency)?$invoice->project->currency:'$'}}</span></span>
                                                                <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="" id="amount">
                                                                <input type="hidden" value="invoice" name="from">
                                                                <input type="hidden" value="{{$invoice->created_by}}" name="invoice_creator">
                                                                <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group mt-3 text-sm-right">
                                                    <button class="btn btn-primary btn-sm rounded-pill" type="button" id="pay_with_razerpay">{{ __('Make Payment') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                    @if(isset($paymentSetting['is_mercado_enabled']) && $paymentSetting['is_mercado_enabled'] == 'on')
                                        <div class="tab-pane fade " id="mercado-payment" role="tabpanel" aria-labelledby="mercado-payment">
                                            <form method="post" action="{{route('invoice.pay.with.mercado')}}" class="require-validation" id="mercado-form">
                                                @csrf
                                                <div class="border p-3 mb-3 rounded">
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label class="form-control-label" for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span class="input-group-text">{{isset($invoice->project->currency)?$invoice->project->currency:'$'}}</span></span>
                                                                <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="" id="amount">
                                                                <input type="hidden" value="invoice" name="from">
                                                                <input type="hidden" value="{{$invoice->created_by}}" name="invoice_creator">
                                                                <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group mt-3 text-sm-right">
                                                    <button class="btn btn-primary btn-sm rounded-pill" type="submit">{{ __('Make Payment') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                    @if(isset($paymentSetting['is_paytm_enabled']) && $paymentSetting['is_paytm_enabled'] == 'on')
                                        <div class="tab-pane fade " id="paytm-payment" role="tabpanel" aria-labelledby="paytm-payment">
                                            <form method="post" action="{{route('invoice.pay.with.paytm')}}" class="require-validation" id="paytm-form">
                                                @csrf
                                                <div class="border p-3 mb-3 rounded">
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label class="form-control-label" for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span class="input-group-text">{{isset($invoice->project->currency)?$invoice->project->currency:'$'}}</span></span>
                                                                <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="" id="amount">
                                                                <input type="hidden" value="invoice" name="from">
                                                                <input type="hidden" value="{{$invoice->created_by}}" name="invoice_creator">
                                                                <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="mobile" class="form-control-label text-dark">{{__('Mobile Number')}}</label>
                                                                <input type="text" id="mobile" name="mobile" class="form-control mobile" data-from="mobile" placeholder="Enter Mobile Number" required="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group mt-3 text-sm-right">
                                                    <button class="btn btn-primary btn-sm rounded-pill" type="submit">{{ __('Make Payment') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                    @if(isset($paymentSetting['is_mollie_enabled']) && $paymentSetting['is_mollie_enabled'] == 'on')
                                        <div class="tab-pane fade " id="mollie-payment" role="tabpanel" aria-labelledby="mollie-payment">
                                            <form method="post" action="{{route('invoice.pay.with.mollie')}}" class="require-validation" id="mollie-form">
                                                @csrf
                                                <div class="border p-3 mb-3 rounded">
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label class="form-control-label" for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span class="input-group-text">{{isset($invoice->project->currency)?$invoice->project->currency:'$'}}</span></span>
                                                                <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="" id="amount">
                                                                <input type="hidden" value="invoice" name="from">
                                                                <input type="hidden" value="{{$invoice->created_by}}" name="invoice_creator">
                                                                <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group mt-3 text-sm-right">
                                                    <button class="btn btn-primary btn-sm rounded-pill" type="submit">{{ __('Make Payment') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                    @if(isset($paymentSetting['is_skrill_enabled']) && $paymentSetting['is_skrill_enabled'] == 'on')
                                        <div class="tab-pane fade " id="skrill-payment" role="tabpanel" aria-labelledby="skrill-payment">
                                            <form method="post" action="{{route('invoice.pay.with.skrill')}}" class="require-validation" id="skrill-form">
                                                @csrf
                                                <div class="border p-3 mb-3 rounded">
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label class="form-control-label" for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span class="input-group-text">{{isset($invoice->project->currency)?$invoice->project->currency:'$'}}</span></span>
                                                                <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="" id="amount">
                                                                <input type="hidden" value="invoice" name="from">
                                                                <input type="hidden" value="{{$invoice->created_by}}" name="invoice_creator">
                                                                <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="error" style="display: none;">
                                                            <div class='alert-danger alert'>{{__('Please correct the errors and try again.')}}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group mt-3 text-sm-right">
                                                    <button class="btn btn-primary btn-sm rounded-pill" type="submit">{{ __('Make Payment') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                    @if(isset($paymentSetting['is_coingate_enabled']) && $paymentSetting['is_coingate_enabled'] == 'on')
                                        <div class="tab-pane fade " id="coingate-payment" role="tabpanel" aria-labelledby="coingate-payment">
                                            <form method="post" action="{{route('invoice.pay.with.coingate')}}" class="require-validation" id="coingate-form">
                                                @csrf
                                                <div class="border p-3 mb-3 rounded">
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label class="form-control-label" for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span class="input-group-text">{{isset($invoice->project->currency)?$invoice->project->currency:'$'}}</span></span>
                                                                <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="" id="amount">
                                                                <input type="hidden" value="invoice" name="from">
                                                                <input type="hidden" value="{{$invoice->created_by}}" name="invoice_creator">
                                                                <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="error" style="display: none;">
                                                            <div class='alert-danger alert'>{{__('Please correct the errors and try again.')}}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group mt-3 text-sm-right">
                                                    <button class="btn btn-primary btn-sm rounded-pill" type="submit">{{ __('Make Payment') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                    @if($paymentSetting['is_paymentwall_enabled'] == 'on')
                                        <div class="tab-pane fade " id="paymentwall-payment" role="tabpanel" aria-labelledby="paymentwall-payment">
                                            <form method="post" action="{{route('invoice.paymentwallpayment')}}" class="require-validation" id="paymentwall-payment-form">
                                                @csrf
                                                <div class="border p-3 mb-3 rounded">
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span class="input-group-text">{{isset($invoice->project->currency)?$invoice->project->currency:'$'}}</span></span>
                                                                <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="" id="amount">
                                                                <input type="hidden" value="invoice" name="from">
                                                                <input type="hidden" value="{{$invoice->created_by}}" name="invoice_creator">
                                                                <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group mt-3 text-sm-right">
                                                    <button class="btn btn-primary btn-sm rounded-pill" type="submit" id="pay_with_paymentwall">{{ __('Make Payment') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @endif
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

        $(document).on('click', '.items_tab', function () {
            $('#from').val($(this).attr('id'));
        })

        $(document).ready(function () {
            setTimeout(function () {
                $('.nav-pills .nav-item a').first().trigger('click');
            }, 100);
        });
    </script>
    @if($invoice->client_id == Auth::user()->id)
        @if($invoice->getDue() > 0 && $paymentSetting['enable_stripe'] == 'on')
            <script src="https://js.stripe.com/v3/"></script>
            <script type="text/javascript">
                var stripe = Stripe('{{ $paymentSetting['stripe_key'] }}');
                var elements = stripe.elements();

                // Custom styling can be passed to options when creating an Element.
                var style = {
                    base: {
                        // Add your base input styles here. For example:
                        fontSize: '14px',
                        color: '#32325d',
                    },
                };

                // Create an instance of the card Element.
                var card = elements.create('card', {style: style});

                // Add an instance of the card Element into the `card-element` <div>.
                card.mount('#card-element');

                // Create a token or display an error when the form is submitted.
                var form = document.getElementById('payment-form');
                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    stripe.createToken(card).then(function (result) {
                        if (result.error) {
                            toastr('Error', result.error.message, 'error');
                        } else {
                            // Send the token to your server.
                            stripeTokenHandler(result.token);
                        }
                    });
                });

                function stripeTokenHandler(token) {
                    // Insert the token ID into the form so it gets submitted to the server
                    var form = document.getElementById('payment-form');
                    var hiddenInput = document.createElement('input');
                    hiddenInput.setAttribute('type', 'hidden');
                    hiddenInput.setAttribute('name', 'stripeToken');
                    hiddenInput.setAttribute('value', token.id);
                    form.appendChild(hiddenInput);

                    // Submit the form
                    form.submit();
                }
            </script>
        @endif

        <script src="{{url('assets/js/jquery.form.js')}}"></script>

        @if(isset($paymentSetting['is_paystack_enabled']) && $paymentSetting['is_paystack_enabled'] == 'on')
            <script src="https://js.paystack.co/v1/inline.js"></script>
            <script>
                //    Paystack Payment
                $(document).on("click", "#pay_with_paystack", function () {

                    $('#paystack-payment-form').ajaxForm(function (res) {
                        if (res.flag == 1) {
                            var coupon_id = res.coupon;

                            var paystack_callback = "{{ url('/invoice/paystack') }}";
                            var order_id = '{{time()}}';
                            var handler = PaystackPop.setup({
                                key: '{{ $paymentSetting['paystack_public_key']  }}',
                                email: res.email,
                                amount: res.total_price * 100,
                                currency: res.currency,
                                ref: 'pay_ref_id' + Math.floor((Math.random() * 1000000000) +
                                    1
                                ), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
                                metadata: {
                                    custom_fields: [{
                                        display_name: "Email",
                                        variable_name: "email",
                                        value: res.email,
                                    }]
                                },

                                callback: function (response) {
                                    console.log(response.reference, order_id);
                                    window.location.href = paystack_callback + '/' + response.reference + '/' + '{{encrypt($invoice->id)}}';
                                },
                                onClose: function () {
                                    alert('window closed');
                                }
                            });
                            handler.openIframe();
                        } else {
                            show_toastr('Error', data.message, 'msg');
                        }

                    }).submit();
                });
            </script>
        @endif

        @if(isset($paymentSetting['is_flutterwave_enabled']) && $paymentSetting['is_flutterwave_enabled'] == 'on')
            <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
            <script>
                //    Flaterwave Payment
                $(document).on("click", "#pay_with_flaterwave", function () {
                    $('#flaterwave-payment-form').ajaxForm(function (res) {
                        if (res.flag == 1) {
                            var coupon_id = res.coupon;

                            var API_publicKey = '{{ $paymentSetting['flutterwave_public_key']  }}';
                            var nowTim = "{{ date('d-m-Y-h-i-a') }}";
                            var flutter_callback = "{{ url('/invoice/flaterwave') }}";
                            var x = getpaidSetup({
                                PBFPubKey: API_publicKey,
                                customer_email: '{{Auth::user()->email}}',
                                amount: res.total_price,
                                currency: res.currency,
                                txref: nowTim + '__' + Math.floor((Math.random() * 1000000000)) + 'fluttpay_online-' +
                                    {{ date('Y-m-d') }},
                                meta: [{
                                    metaname: "payment_id",
                                    metavalue: "id"
                                }],
                                onclose: function () {
                                },
                                callback: function (response) {
                                    var txref = response.tx.txRef;
                                    if (
                                        response.tx.chargeResponseCode == "00" ||
                                        response.tx.chargeResponseCode == "0"
                                    ) {
                                        window.location.href = flutter_callback + '/' + txref + '/' + '{{\Illuminate\Support\Facades\Crypt::encrypt($invoice->id)}}';
                                    } else {
                                        // redirect to a failure page.
                                    }
                                    x.close(); // use this to close the modal immediately after payment.
                                }
                            });

                        } else {
                            show_toastr('Error', res.msg, 'msg');
                        }

                    }).submit();
                });
            </script>
        @endif

        @if(isset($paymentSetting['is_razorpay_enabled']) && $paymentSetting['is_razorpay_enabled'] == 'on')
            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
            <script>
                $('#razorpay-payment-form').ajaxForm(function (res) {
                    if(res.flag == 1){
                        var razorPay_callback = "{{url('/invoice-pay-with-razorpay')}}";
                        var totalAmount = res.total_price * 100;
                        var coupon_id = res.coupon;
                        var API_publicKey = '';
                        if("{{isset($paymentSetting['razorpay_public_key'])}}"){
                            API_publicKey = "{{$paymentSetting['razorpay_public_key']}}";
                        }
                        var options = {
                            "key": API_publicKey, // your Razorpay Key Id
                            "amount": totalAmount,
                            "name": 'Invoice Payment',
                            "currency": res.currency,
                            "description": "",
                            "handler": function (response) {
                                window.location.href = "{{url('/invoice/razorpay')}}/"+response.razorpay_payment_id +"/{{encrypt($invoice->id)}}";
                            },
                            "theme": {
                                "color": "#528FF0"
                            }
                        };
                        var rzp1 = new Razorpay(options);
                        rzp1.open();
                    }else if(res.flag == 2){

                    }else{
                        toastrs('Error', data.message, 'msg');
                    }
                }).submit();
            </script>
        @endif
    @endif
<script>
    $('.cp_link').on('click', function () {
        var value = $(this).attr('data-link');
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(value).select();
        document.execCommand("copy");
        $temp.remove();
        show_toastr('Success', '{{__('Link Copy on Clipboard')}}', 'success')
    });

</script>
@endpush
