@extends('layouts.invoicepayheader')
@section('title')
    {{__('Invoice')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Invoice')}} {{ '('. $invoice->name .')' }}</h5>
    </div>
@endsection
@section('action-button')
    <a href="{{route('invoice.download.pdf',\Crypt::encrypt($invoice->id))}}" target="_blank" class="btn btn-sm btn-primary btn-icon rounded-pill">
        <span class="btn-inner--icon text-white"><i class="fa fa-print"></i></span>
        <span class="btn-inner--text text-white">{{__('Print')}}</span>
    </a>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-body">
                    <dl class="row">
                        <div class="col-12">
                            <div class="row align-items-center mb-5">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                </div>
                                <div class="col-sm-6 text-sm-right">
                                    <h6 class="d-inline-block m-0 d-print-none">{{__('Invoice')}}</h6>

                                    @if($invoice->status == 0)
                                        <span class="badge badge-primary">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                    @elseif($invoice->status == 1)
                                        <span class="badge badge-danger">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                    @elseif($invoice->status == 2)
                                        <span class="badge badge-warning">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                    @elseif($invoice->status == 3)
                                        <span class="badge badge-success">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                    @elseif($invoice->status == 4)
                                        <span class="badge badge-info">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-8">
                                    <h6 class="d-inline-block m-0 d-print-none">{{__('Invoice ID')}}</h6>
                                    <span class="col-sm-8">
                                        <span class="text-sm">{{ App\Models\User::invoiceNumberFormat($invoice->id) }}</span>
                                    </span>
                                </div>
                            </div>
                            <div class="row mb-5">
                                <div class="col-lg-6 col-md-8">
                                    <h6 class="d-inline-block m-0 d-print-none">{{__('Invoice Date')}}</h6>
                                    <span class="col-sm-8"><span class="text-sm">{{App\Models\User::dateFormat($invoice->date_invoiced)}}</span></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <h5>{{__('Item List')}}</h5>
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <thead>
                                            <tr>
                                                <th class="px-0 bg-transparent border-top-0">{{__('Item')}}</th>
                                                <th class="px-0 bg-transparent border-top-0">{{__('Price')}}</th>
                                                <th class="px-0 bg-transparent border-top-0">{{__('Tax')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php
                                                $totalQuantity=0;
                                                $totalRate=0;
                                                $totalAmount=0;
                                                $totalTaxPrice=0;
                                                $totalDiscount=0;
                                                $taxesData=[];
                                            @endphp
                                            @foreach($invoice->items as $invoiceitem)
                                                @php
                                                    // $taxes=App\Models\Utility::tax($invoiceitem->tax);
                                                    $totalQuantity+=$invoiceitem->quantity;
                                                    $totalRate+=$invoiceitem->price;
                                                    $totalDiscount+=$invoiceitem->discount;
                                                    if(!empty($taxes[0]))
                                                    {
                                                        foreach($taxes as $taxe)
                                                        {
                                                            $taxDataPrice=App\Models\Utility::taxRate($taxe->rate,$invoiceitem->price,$invoiceitem->quantity);
                                                            if (array_key_exists($taxe->tax_name,$taxesData))
                                                            {
                                                                $taxesData[$taxe->tax_name] = $taxesData[$taxe->tax_name]+$taxDataPrice;
                                                            }
                                                            else
                                                            {
                                                                $taxesData[$taxe->tax_name] = $taxDataPrice;
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <tr>
                                                    <td class="px-0">{{$invoiceitem->item}} </td>
                                            
                                                    <td class="px-0">{{App\Models\User::priceFormat($invoiceitem->price)}} </td>
                                                   
                                                        <td class="px-0">{{ \App\Models\Utility::projectCurrencyFormat($invoice->project_id,$item->tax(),true) }}</td>
                                                  
                                                    {{-- <td class="px-0">{{App\Models\User::priceFormat($invoiceitem->discount)}} </td> --}}
                                                    {{-- <td class="px-0">{{$invoiceitem->description}} </td>
                                                    <td class="text-right"> {{App\Models\User::priceFormat($invoiceitem->price*$invoiceitem->quantity)}}</td> --}}
                                                    
                                                    {{-- @php
                                                        $totalQuantity+=$invoiceitem->quantity;
                                                        $totalRate+=$invoiceitem->price;
                                                        $totalDiscount+=$invoiceitem->discount;
                                                        $totalAmount+=($invoiceitem->price*$invoiceitem->quantity);
                                                    @endphp --}}
                                                </tr>
                                            @endforeach
                                            <tfoot>
                                            <tr>
                                                <td colspan="4">&nbsp;</td>
                                                <td class="px-0"></td>
                                                <td class="text-right"><strong>{{__('Sub Total')}}</strong></td>
                                        
                                                <td class="text-right subTotal">{{App\Models\User::priceFormat($invoice->getSubTotal())}}</td>
                                               
                                            </tr>

                                            <tr>
                                                <td colspan="4">&nbsp;</td>
                                                <td class="px-0"></td>
                                                <td class="text-right"><strong>{{__('Discount')}}</strong></td>
                                                <td class="text-right subTotal">{{App\Models\User::priceFormat($invoice->getTotalDiscount())}}</td>
                                                
                                            </tr>
                                            @if(!empty($taxesData))
                                                @foreach($taxesData as $taxName => $taxPrice)
                                                    @if($taxName != 'No Tax')
                                                        <tr>
                                                            <td colspan="4"></td>
                                                            <td class="px-0"></td>
                                                            <td class="text-right"><b>{{$taxName}}</b></td>
                                                            <td class="text-right">{{ App\Models\User::priceFormat($taxPrice) }}</td>
                                                            
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endif
                                            <tr>
                                                <td colspan="4">&nbsp;</td>
                                                <td class="px-0"></td>
                                                <td class="text-right"><strong>{{__('Total')}}</strong></td>
                                                <td class="text-right subTotal">{{App\Models\User::priceFormat( $invoice->getTotal())}}</td>
                                               
                                            </tr>
                                            </tfoot>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card my-5 bg-secondary">
                                        <div class="card-body">
                                            <div class="row justify-content-between align-items-center">
                                                <div class="col-md-6 order-md-2 mb-4 mb-md-0">
                                                    <div class="d-flex align-items-center justify-content-md-end">
                                                        <span class="h6 text-muted d-inline-block mr-3 mb-0">{{__('Total value')}}:</span>
                                                        <span class="h4 mb-0">{{App\Models\User::priceFormat($invoice->getTotal())}}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 order-md-1">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <h5>{{__('From')}}</h5>
                                    <dl class="row mt-4 align-items-center">
                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Company Address')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $company_setting['company_address'] }}</span></dd>

                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Company City')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $company_setting['company_city'] }}</span></dd>

                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Company Country')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $company_setting['company_country'] }}</span></dd>

                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Zip Code')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $company_setting['company_zipcode'] }}</span></dd>

                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Company Contact')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $company_setting['company_telephone']}}</span></dd>
                                    </dl>
                                </div>
                                <div class="col-12 col-md-4">
                                    <h5>{{__('Billing Address')}}</h5>
                                    <dl class="row mt-4 align-items-center">
                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Billing Address')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $invoice->billing_address }}</span></dd>

                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Billing City')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $invoice->billing_city }}</span></dd>

                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Billing Country')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $invoice->billing_country }}</span></dd>

                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Zip Code') }}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $invoice->billing_postalcode }}</span></dd>

                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Billing Contact')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ !empty($invoice->contacts->name)?$invoice->contacts->name:'--'}}</span></dd>
                                    </dl>
                                </div>
                                <div class="col-12 col-md-4">
                                    <h5>{{__('Shipping Address')}}</h5>
                                    <dl class="row mt-4 align-items-center">
                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Shipping Address')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $invoice->shipping_address }}</span></dd>

                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Shipping City')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $invoice->shipping_city }}</span></dd>

                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Shipping Country')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $invoice->shipping_country }}</span></dd>

                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Zip Code')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ $invoice->shipping_postalcode }}</span></dd>

                                        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Shipping Contact')}}</span></dt>
                                        <dd class="col-sm-8"><span class="text-sm">{{ !empty($invoice->contacts->name)?$invoice->contacts->name:'--'}}</span></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="card">
                <div class="card-footer py-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <div class="row align-items-center">
                                <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{__('Assigned User')}}</span></dt>
                                <dd class="col-sm-12"><span class="text-sm">{{ !empty($invoice->assign_user)?$invoice->assign_user->name:''}}</span></dd>

                                <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{__('Created')}}</span></dt>
                                <dd class="col-sm-12"><span class="text-sm">{{App\Models\User::dateFormat($invoice->created_at)}}</span></dd>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-10">
            <div class="row">
                <div class="col-12">
                    <h4 class="h4 font-weight-400 float-left">{{__('Payment History')}}</h4>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                <tr>
                                    <th>{{__('Transaction ID')}}</th>
                                    <th>{{__('Payment Date')}}</th>
                                    <th>{{__('Payment Method')}}</th>
                                    <th>{{__('Payment Type')}}</th>
                                    <th>{{__('Note')}}</th>
                                    <th class="text-right">{{__('Amount')}}</th>
                                </tr>
                                </thead>
                                <tbody class="list">
                                    @php $i=0; @endphp
                                    @foreach($invoice->payments as $payment)
                                        <tr>
                                            <td>{{sprintf("%05d", $payment->transaction_id)}}</td>
                                            <td>{{ App\Models\User::dateFormat($payment->date) }}</td>
                                            <td>{{(!empty($payment->payment)?$payment->payment->name:'-')}}</td>
                                            <td>{{$payment->payment_type}}</td>
                                            <td>{{!empty($payment->notes) ? $payment->notes : '-'}}</td>
                                            <td class="text-right">{{App\Models\User::priceFormat($payment->amount)}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($invoice->getDue() > 0)
        <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentModalLabel">{{ __('Add Payment') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <ul class="nav nav-tabs nav-overflow profile-tab-list" role="tablist">
                               
                                @if(isset($payment_setting['enable_stripe']) && $payment_setting['enable_stripe'] == 'on')
                                    @if((isset($payment_setting['stripe_key']) && !empty($payment_setting['stripe_key'])) && 
                                    (isset($payment_setting['stripe_secret']) && !empty($payment_setting['stripe_secret'])))
                                        
                                        <li class="nav-item ml-4">
                                            <a href="#stripe-payment" class="nav-link active" data-toggle="tab" role="tab" aria-selected="false">
                                                {{ __('Stripe') }}
                                            </a>
                                        </li>
                                    @endif
                                @endif
                               
                                @if(isset($payment_setting['enable_paypal']) && $payment_setting['enable_paypal'] == 'on')
                                    @if((isset($payment_setting['paypal_client_id']) && !empty($payment_setting['paypal_client_id'])) && 
                                    (isset($payment_setting['paypal_secret_key']) && !empty($payment_setting['paypal_secret_key'])))
                                        <li class="nav-item ml-4">
                                            <a data-toggle="tab" href="#paypal-payment" class="nav-link" role="tab" aria-controls="paypal" aria-selected="false">{{ __('Paypal') }}</a>
                                        </li>
                                    @endif
                                @endif
                                @if(isset($payment_setting['is_paystack_enabled']) && $payment_setting['is_paystack_enabled'] == 'on')
                                    @if((isset($payment_setting['paystack_public_key']) && !empty($payment_setting['paystack_public_key'])) && 
                                    (isset($payment_setting['paystack_secret_key']) && !empty($payment_setting['paystack_secret_key'])))
                                        <li class="nav-item ml-4">
                                            <a data-toggle="tab" href="#paystack-payment" class="nav-link" role="tab" aria-controls="paystack" aria-selected="false">{{ __('Paystack') }}</a>
                                        </li>
                                    @endif
                                @endif
                                @if(isset($payment_setting['is_flutterwave_enabled']) && $payment_setting['is_flutterwave_enabled'] == 'on')
                                    @if((isset($payment_setting['flutterwave_secret_key']) && !empty($payment_setting['flutterwave_secret_key'])) && 
                                    (isset($payment_setting['flutterwave_public_key']) && !empty($payment_setting['flutterwave_public_key'])))
                                        <li class="nav-item ml-4">
                                            <a data-toggle="tab" href="#flutterwave-payment" class="nav-link" role="tab" aria-controls="flutterwave" aria-selected="false">{{ __('Flutterwave') }}</a>
                                        </li>
                                    @endif
                                @endif
                                @if(isset($payment_setting['is_razorpay_enabled']) && $payment_setting['is_razorpay_enabled'] == 'on')
                                    @if((isset($payment_setting['razorpay_public_key']) && !empty($payment_setting['razorpay_public_key'])) && 
                                    (isset($payment_setting['razorpay_secret_key']) && !empty($payment_setting['razorpay_secret_key'])))
                                        <li class="nav-item ml-4">
                                            <a data-toggle="tab" href="#razorpay-payment" class="nav-link" role="tab" aria-controls="razorpay" aria-selected="false">{{ __('Razorpay') }}</a>
                                        </li>
                                    @endif
                                @endif
                                @if(isset($payment_setting['is_mercado_enabled']) && $payment_setting['is_mercado_enabled'] == 'on')
                                    @if((isset($payment_setting['mercado_app_id']) && !empty($payment_setting['mercado_app_id'])) && 
                                    (isset($payment_setting['mercado_secret_key']) && !empty($payment_setting['mercado_secret_key'])))
                                        <li class="nav-item ml-4">
                                            <a data-toggle="tab" href="#mercado-payment" class="nav-link" role="tab" aria-controls="mercado" aria-selected="false">{{ __('Mercado Pago') }}</a>
                                        </li>
                                    @endif
                                @endif
                                @if(isset($payment_setting['is_paytm_enabled']) && $payment_setting['is_paytm_enabled'] == 'on')
                                    @if((isset($payment_setting['paytm_merchant_id']) && !empty($payment_setting['paytm_merchant_id'])) && 
                                    (isset($payment_setting['paytm_merchant_key']) && !empty($payment_setting['paytm_merchant_key'])))
                                        <li class="nav-item ml-4">
                                            <a data-toggle="tab" href="#paytm-payment" class="nav-link" role="tab" aria-controls="paytm" aria-selected="false">{{ __('Paytm') }}</a>
                                        </li>
                                    @endif
                                @endif
                                @if(isset($payment_setting['is_mollie_enabled']) && $payment_setting['is_mollie_enabled'] == 'on')
                                    @if((isset($payment_setting['mollie_api_key']) && !empty($payment_setting['mollie_api_key'])) && 
                                    (isset($payment_setting['mollie_profile_id']) && !empty($payment_setting['mollie_profile_id'])))
                                        <li class="nav-item ml-4">
                                            <a data-toggle="tab" href="#mollie-payment" class="nav-link" role="tab" aria-controls="mollie" aria-selected="false">{{ __('Mollie') }}</a>
                                        </li>
                                    @endif
                                @endif
                                @if(isset($payment_setting['is_skrill_enabled']) && $payment_setting['is_skrill_enabled'] == 'on')
                                    @if((isset($payment_setting['skrill_email']) && !empty($payment_setting['skrill_email'])))
                                        <li class="nav-item ml-4">
                                            <a data-toggle="tab" href="#skrill-payment" class="nav-link" role="tab" aria-controls="skrill" aria-selected="false">{{ __('Skrill') }}</a>
                                        </li>
                                    @endif
                                @endif
                                @if(isset($payment_setting['is_coingate_enabled']) && $payment_setting['is_coingate_enabled'] == 'on')
                                    @if((isset($payment_setting['coingate_auth_token']) && !empty($payment_setting['coingate_auth_token'])))
                                        <li class="nav-item ml-4">
                                            <a data-toggle="tab" href="#coingate-payment" class="nav-link" role="tab" aria-controls="coingate" aria-selected="false">{{ __('CoinGate') }}</a>
                                        </li>
                                    @endif
                                @endif
                            </ul>
                            <div class="tab-content"> 
                                <div class="tab-pane fade show active" id="stripe-payment" role="tabpanel" aria-labelledby="stripe-payment-tab">
                                    <div class="card-body">
                                        @if(isset($payment_setting['enable_stripe']) && $payment_setting['enable_stripe'] == 'on')
                                            @if((isset($payment_setting['stripe_key']) && !empty($payment_setting['stripe_key'])) && 
                                                (isset($payment_setting['stripe_secret']) && !empty($payment_setting['stripe_secret'])))
                                                
                                                <form class="w3-container w3-display-middle w3-card-4 " method="POST" id="payment-form" action="{{ route('invoice.pay.with.stripe') }}">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                            <div class="form-icon-addon">
                                                                <span>{{ $payment_setting['currency_symbol'] }}</span>
                                                                <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                                <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                            </div>
                                                            @error('amount')
                                                            <span class="invalid-amount text-danger text-xs" role="alert">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                        <div class="col-12 form-group mt-3 text-right">
                                                            <input type="submit" value="{{__('Make Payment')}}" class="btn btn-sm btn-primary rounded-pill">
                                                        </div>
                                                    </div>
                                                </form>
                                                
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="paypal-payment" role="tabpanel" aria-labelledby="paypal-payment-tab">
                                    <div class="card-body">
                                        @if(isset($payment_setting['enable_paypal']) && $payment_setting['enable_paypal'] == 'on')
                                            @if((isset($payment_setting['paypal_client_id']) && !empty($payment_setting['paypal_client_id'])) && 
                                                (isset($payment_setting['paypal_secret_key']) && !empty($payment_setting['paypal_secret_key'])))
                                               
                                                <form class="w3-container w3-display-middle w3-card-4 " method="POST" id="payment-form" action="{{ route('client.pay.with.paypal', $invoice->id) }}">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                            <div class="form-icon-addon">
                                                                <span>{{ $payment_setting['currency_symbol'] }}</span>
                                                                <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                            </div>
                                                            @error('amount')
                                                            <span class="invalid-amount text-danger text-xs" role="alert">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                        <div class="col-12 form-group mt-3 text-right">
                                                            <input type="submit" value="{{__('Make Payment')}}" class="btn btn-sm btn-primary rounded-pill">
                                                        </div>
                                                    </div>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="paystack-payment" role="tabpanel" aria-labelledby="paystack-payment-tab">
                                    <div class="card-body">
                                        @if(isset($payment_setting['is_paystack_enabled']) && $payment_setting['is_paystack_enabled'] == 'on')
                                            @if((isset($payment_setting['paystack_public_key']) && !empty($payment_setting['paystack_public_key'])) && 
                                                (isset($payment_setting['paystack_secret_key']) && !empty($payment_setting['paystack_secret_key'])))
                                                
                                                <form method="post" action="{{route('invoice.pay.with.paystack')}}" class="require-validation" id="paystack-payment-form">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="Email" class="form-control-label">{{ __('Email') }}</label>
                                                            <div class="form-icon-addon">
                                                                <span class="fa fa-envelope"></span>
                                                                <input class="form-control" required="required" id="paystack_email" name="email" type="email" placeholder="Enter Email">
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-12">
                                                            <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                            <div class="form-icon-addon">
                                                                <span>{{isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'}}</span>
                                                                <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                                <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-right">
                                                        <input type="button" value="{{__('Make Payment')}}" class="btn btn-sm btn-primary rounded-pill" id="pay_with_paystack">
                                                    </div>
                                                </form>
                                               
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="flutterwave-payment" role="tabpanel" aria-labelledby="flutterwave-payment-tab">
                                    <div class="card-body">
                                        @if(isset($payment_setting['is_flutterwave_enabled']) && $payment_setting['is_flutterwave_enabled'] == 'on')
                                            @if((isset($payment_setting['flutterwave_secret_key']) && !empty($payment_setting['flutterwave_secret_key'])) && 
                                                (isset($payment_setting['flutterwave_public_key']) && !empty($payment_setting['flutterwave_public_key'])))
                                                
                                                <form method="post" action="{{route('invoice.pay.with.flaterwave')}}" class="require-validation" id="flaterwave-payment-form">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="Email" class="form-control-label">{{ __('Email') }}</label>
                                                            <div class="form-icon-addon">
                                                                <span class="fa fa-envelope"></span>
                                                                <input class="form-control" required="required" id="flutterwave_email" name="email" type="email" placeholder="Enter Email">
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-12">
                                                            <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                            <div class="form-icon-addon">
                                                                <span>{{isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'}}</span>
                                                                <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                                <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-right">
                                                        <input type="button" value="{{__('Make Payment')}}" class="btn btn-sm btn-primary rounded-pill" id="pay_with_flaterwave">
                                                    </div>
                                                </form>
                                                
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="razorpay-payment" role="tabpanel" aria-labelledby="razorpay-payment-tab">
                                    <div class="card-body">
                                        @if(isset($payment_setting['is_razorpay_enabled']) && $payment_setting['is_razorpay_enabled'] == 'on')
                                            @if((isset($payment_setting['razorpay_public_key']) && !empty($payment_setting['razorpay_public_key'])) && 
                                                (isset($payment_setting['razorpay_secret_key']) && !empty($payment_setting['razorpay_secret_key'])))
                                                
                                                    <form method="post" action="{{route('invoice.pay.with.razorpay')}}" class="require-validation" id="razorpay-payment-form">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="form-group col-md-12">
                                                                <label for="Email" class="form-control-label">{{ __('Email') }}</label>
                                                                <div class="form-icon-addon">
                                                                    <span class="fa fa-envelope"></span>
                                                                    <input class="form-control" required="required" id="razorpay_email" name="email" type="email" placeholder="Enter Email">
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-md-12">
                                                                <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                                <div class="form-icon-addon">
                                                                    <span>{{isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'}}</span>
                                                                    <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                                    <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 form-group mt-3 text-right">
                                                            <input type="submit" value="{{__('Make Payment')}}" class="btn btn-sm btn-primary rounded-pill" id="pay_with_razorpay">
                                                        </div>
                                                    </form>
                                                
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="mollie-payment" role="tabpanel" aria-labelledby="mollie-payment-tab">
                                    <div class="card-body">
                                    @if(isset($payment_setting['is_mollie_enabled']) && $payment_setting['is_mollie_enabled'] == 'on')
                                        @if((isset($payment_setting['mollie_api_key']) && !empty($payment_setting['mollie_api_key'])) && 
                                            (isset($payment_setting['mollie_profile_id']) && !empty($payment_setting['mollie_profile_id'])))
                                            
                                            <form method="post" action="{{route('invoice.pay.with.mollie')}}" class="require-validation" id="mollie-payment-form">
                                                @csrf
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                        <div class="form-icon-addon">
                                                            <span>{{isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'}}</span>
                                                            <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                            <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 form-group mt-3 text-right">
                                                    <input type="submit" value="{{__('Make Payment')}}" class="btn btn-sm btn-primary rounded-pill">
                                                </div>
                                            </form>
                                        @endif
                                    @endif
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="mercado-payment" role="tabpanel" aria-labelledby="mercado-payment-tab">
                                    <div class="card-body">
                                        @if(isset($payment_setting['is_mercado_enabled']) && $payment_setting['is_mercado_enabled'] == 'on')
                                            @if((isset($payment_setting['mercado_app_id']) && !empty($payment_setting['mercado_app_id'])) && 
                                                (isset($payment_setting['mercado_secret_key']) && !empty($payment_setting['mercado_secret_key'])))
                                               
                                                <form method="post" action="{{route('invoice.pay.with.mercado')}}" class="require-validation" id="mercado-payment-form">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                            <div class="form-icon-addon">
                                                                <span>{{isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'}}</span>
                                                                <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                                <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-right">
                                                        <input type="submit" value="{{__('Make Payment')}}" class="btn btn-sm btn-primary rounded-pill">
                                                    </div>
                                                </form>
                                               
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="paytm-payment" role="tabpanel" aria-labelledby="paytm-payment-tab">
                                    <div class="card-body">
                                    @if(isset($payment_setting['is_paytm_enabled']) && $payment_setting['is_paytm_enabled'] == 'on')
                                        @if((isset($payment_setting['paytm_merchant_id']) && !empty($payment_setting['paytm_merchant_id'])) && 
                                            (isset($payment_setting['paytm_merchant_key']) && !empty($payment_setting['paytm_merchant_key'])))
                                            
                                                <form method="post" action="{{route('invoice.pay.with.paytm')}}" class="require-validation" id="paytm-payment-form">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                           
                                                            <label for="Email" class="form-control-label">{{ __('Email') }}</label>
                                                            <div class="form-icon-addon">
                                                                <span class="fa fa-envelope"></span>
                                                                <input class="form-control" required="required" id="paytm_email" name="email" type="email" placeholder="Enter Email">
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-12">
                                                            <label for="mobile" class="form-control-label text-dark">{{__('Mobile Number')}}</label>
                                                            <div class="form-icon-addon">
                                                                <span class="fa fa-phone"></span>
                                                                <input type="text" id="mobile" name="mobile" class="form-control mobile" data-from="mobile" placeholder="{{ __('Enter Mobile Number') }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-12">
                                                            <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                            <div class="form-icon-addon">
                                                                <span>{{isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'}}</span>
                                                                <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                                <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-right">
                                                        <input type="submit" value="{{__('Make Payment')}}" class="btn btn-sm btn-primary rounded-pill">
                                                    </div>
                                                </form>
                                            
                                        @endif
                                    @endif
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="skrill-payment" role="tabpanel" aria-labelledby="skrill-payment-tab">
                                    <div class="card-body">
                                        @if(isset($payment_setting['is_skrill_enabled']) && $payment_setting['is_skrill_enabled'] == 'on')
                                            @if((isset($payment_setting['skrill_email']) && !empty($payment_setting['skrill_email'])))
                                               
                                                <form method="post" action="{{route('invoice.pay.with.skrill')}}" class="require-validation" id="skrill-payment-form">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="form-group col-md-6">
                                                           
                                                            <label for="Name" class="form-control-label">{{ __('Name') }}</label>
                                                            <div class="form-icon-addon">
                                                                <span class="fa fa-user"></span>
                                                                <input class="form-control" required="required" id="skrill_name" name="name" type="text" placeholder="Enter your name">
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                           
                                                            <label for="Email" class="form-control-label">{{ __('Email') }}</label>
                                                            <div class="form-icon-addon">
                                                                <span class="fa fa-envelope"></span>
                                                                <input class="form-control" required="required" id="skrill_email" name="email" type="email" placeholder="Enter Email">
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-12">
                                                            <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                            <div class="form-icon-addon">
                                                                <span>{{isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'}}</span>
                                                                <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                                <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-12 form-group mt-3 text-right">
                                                        <input type="submit" value="{{__('Make Payment')}}" class="btn btn-sm btn-primary rounded-pill">
                                                    </div>
                                                </form>
                                                
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="coingate-payment" role="tabpanel" aria-labelledby="coingate-payment-tab">
                                    <div class="card-body">
                                        @if(isset($payment_setting['is_coingate_enabled']) && $payment_setting['is_coingate_enabled'] == 'on')
                                            @if((isset($payment_setting['coingate_auth_token']) && !empty($payment_setting['coingate_auth_token'])))
                                                
                                                <form method="post" action="{{route('invoice.pay.with.coingate')}}" class="require-validation" id="coingate-payment-form">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                            <div class="form-icon-addon">
                                                                <span>{{isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'}}</span>
                                                                <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                                <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-right">
                                                        <input type="submit" value="{{__('Make Payment')}}" class="btn btn-sm btn-primary rounded-pill">
                                                    </div>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('script')
<script src="{{url('assets/js/jquery.form.js')}}"></script>
    @if($invoice->getDue() > 0 && isset($payment_setting['is_stripe_enabled']) && $payment_setting['is_stripe_enabled'] == 'on')
        <?php $stripe_session = Session::get('stripe_session');?>
        <?php if(isset($stripe_session) && $stripe_session): ?>
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            var stripe = Stripe('{{ $payment_setting['stripe_key'] }}');
            stripe.redirectToCheckout({
                sessionId: '{{ $stripe_session->id }}',
            }).then((result) => {
              //  console.log(result);
            });
        </script>
        <?php endif ?>
    @endif

    @if($invoice->getDue() > 0 && isset($payment_setting['is_paystack_enabled']) && $payment_setting['is_paystack_enabled'] == 'on')

        <script src="https://js.paystack.co/v1/inline.js"></script>

       
        <script type="text/javascript">
            $(document).on("click", "#pay_with_paystack", function () {
                $('#paystack-payment-form').ajaxForm(function (res) {
                    if(res.flag == 1){
                        var coupon_id = res.coupon;
                        var paystack_callback = "{{ url('/invoice-pay-with-paystack') }}";
                        var order_id = '{{time()}}';
                        var handler = PaystackPop.setup({
                            key: '{{ $payment_setting['paystack_public_key']  }}',
                            email: res.email,
                            amount: res.total_price*100,
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
                            callback: function(response) {
                                window.location.href = "{{url('/invoice/paystack')}}/"+response.reference+"/{{encrypt($invoice->id)}}";
                            },
                            onClose: function() {
                                alert('window closed');
                            }
                        });
                        handler.openIframe();
                    }else if(res.flag == 2){
                    }else{
                        show_toastr('Error', data.message, 'msg');
                    }
                }).submit();
            });
        </script>
    @endif

    @if($invoice->getDue() > 0 && isset($payment_setting['is_flutterwave_enabled']) && $payment_setting['is_flutterwave_enabled'] == 'on')

        <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>

        <script type="text/javascript">

            //    Flaterwave Payment
            $(document).on("click", "#pay_with_flaterwave", function () {

                $('#flaterwave-payment-form').ajaxForm(function (res) {
                    if(res.flag == 1){
                        var coupon_id = res.coupon;
                        var API_publicKey = '';
                        if("{{ isset($payment_setting['flutterwave_public_key'] ) }}"){
                            API_publicKey = "{{$payment_setting['flutterwave_public_key']}}";
                        }
                        var nowTim = "{{ date('d-m-Y-h-i-a') }}";
                        var flutter_callback = "{{ url('/invoice-pay-with-flaterwave') }}";
                        var x = getpaidSetup({
                            PBFPubKey: API_publicKey,
                            customer_email: res.email,
                            amount: res.total_price,
                            currency: '{{$payment_setting['currency']}}',
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
                                if(response.tx.chargeResponseCode == "00" || response.tx.chargeResponseCode == "0") {
                                    window.location.href = "{{url('/invoice/flaterwave')}}/"+txref+"/{{encrypt($invoice->id)}}";
                                }else{
                                    // redirect to a failure page.
                                }
                                x.close(); // use this to close the modal immediately after payment.
                            }});
                    }else if(res.flag == 2){

                    }else{
                        show_toastr('Error', data.message, 'msg');
                    }

                }).submit();
            });
        </script>

    @endif

    @if($invoice->getDue() > 0 && isset($payment_setting['is_razorpay_enabled']) && $payment_setting['is_razorpay_enabled'] == 'on')
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script type="text/javascript">
            // Razorpay Payment
            $(document).on("click", "#pay_with_razorpay", function () {
                $('#razorpay-payment-form').ajaxForm(function (res) {
                    console.log('hry');
                    if(res.flag == 1){
                        var razorPay_callback = "{{url('/invoice-pay-with-razorpay')}}";
                        var totalAmount = res.total_price * 100;
                        var coupon_id = res.coupon;
                        var API_publicKey = '';
                        if("{{isset($payment_setting['razorpay_public_key'])}}"){
                            API_publicKey = "{{$payment_setting['razorpay_public_key']}}";
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
            });
        </script>
    
    @endif
@endpush
