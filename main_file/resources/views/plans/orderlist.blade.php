@extends('layouts.admin')

@section('title')
    {{__('Orders')}}
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" width="100%">
                            <thead class="thead-light">
                            <tr>
                                <th>{{__('Order Id')}}</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Plan Name')}}</th>
                                <th>{{__('Price')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Type')}}</th>
                                <th>{{__('Date')}}</th>
                                <th>{{__('Coupon')}}</th>
                                <th class="text-right">{{__('Invoice')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($orders->count() > 0)
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{$order->order_id}}</td>
                                        <td>{{$order->user_name}}</td>
                                        <td>{{$order->plan_name}}</td>
                                        <td>{{(env('CURRENCY') ? env('CURRENCY') : '$')}} {{number_format($order->price)}}</td>
                                        <td>
                                            @if($order->payment_status == 'succeeded' || $order->payment_status == 'approved')
                                                <span class="badge badge-success">{{ucfirst($order->payment_status)}}</span>
                                            @else
                                                <span class="badge badge-danger">{{ucfirst($order->payment_status)}}</span>
                                            @endif
                                        </td>
                                        <td>{{$order->payment_type}}</td>
                                        <td>{{$order->created_at->format('d M Y')}}</td>
                                        <td>{{!empty($order->use_coupon)?$order->use_coupon->coupon_detail->name:'-'}}</td>
                                        <td class="text-right">
                                            @if($order->receipt =='free coupon')
                                                <p>{{__('Used 100 % discount coupon code.')}}</p>
                                            @elseif(!empty($order->receipt))
                                                <a href="{{$order->receipt}}" target="_blank" class="text-primary"><i class="fas fa-file-invoice"></i></a>
                                            @else
                                                {{'-'}}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <th scope="col" colspan="8"><h6 class="text-center">{{__('No Orders Found.')}}</h6></th>
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
