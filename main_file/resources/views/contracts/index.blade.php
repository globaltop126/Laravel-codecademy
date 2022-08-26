@extends('layouts.admin')

@section('title')
    {{ __('Manage Contract') }}
@endsection

@section('action-button')
   
@if (Auth::user()->type == 'owner')
    
    <a href="#" class="btn btn-sm btn-white btn-icon-only rounded-circle ml-2" data-url="{{ route('contractclient.create') }}" data-ajax-popup="true" data-size="lg" data-title="{{__('Create Contract')}}">
        <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
    </a>
    @endif

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-3 col-6">
            <div class="card comp-card size">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{__('Total Contracts')}}</h6>
                            <h6 class="text-primary">{{ $cnt_contract['total'] }}</h6>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake bg-success text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div> 

       

        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{__('This Month Total Contracts')}}</h6>
                            <h6 class="text-info">{{ $cnt_contract['this_month'] }}</h6>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake bg-info text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{__('This Week Total Contracts')}}</h6>
                            <h6 class="text-warning">{{ $cnt_contract['this_week'] }}</h6>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake bg-warning text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-6">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{__('Last 30 Days Total Contracts')}}</h6>
                            <h6 class="text-danger">{{ $cnt_contract['last_30days'] }}</h6>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake bg-danger text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable" id="selection-datatable">
                            <thead>
                                <tr>
                                        <th>{{ __('Contract') }}</th>
                                    <th>{{ __('Project') }}</th>
                                      <th>{{ __('Subject') }}</th>
                                    <th>{{ __('Client Name') }}</th>
                                    <th>{{ __('Value') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Start Date') }}</th>
                                    <th>{{ __('End Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th width="250px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contracts as $contract)
                                    <tr>    
                                        
                                        <td class="id">
                                            <a href="{{ route('contractclient.show', $contract->id) }}" class="btn btn-outline-primary">{{ App\Models\Utility::contractNumberFormat($contract->id) }}</a>
                                        </td>
                                        <td>{{!empty($contract->getprojectname) ? $contract->getprojectname->name : '-' }} </td>
                                         <td>{{$contract->subject}} </td>    
                                        <td>{{ !empty($contract->clientdetail) ? $contract->clientdetail->name : '-' }}</td>
                                        <td>{{ Auth::user()->priceFormat($contract->value) }}</td>
                                        <td>{{ !empty($contract->ContractType) ? $contract->ContractType->name : '-' }}</td>
                                        <td>{{ Auth::user()->dateFormat($contract->start_date) }}</td>
                                        <td>{{ Auth::user()->dateFormat($contract->end_date) }}</td>
                                        <td>


                                                @if($contract->status == 'close')
                                                  <span class="badge badge-pill badge-danger">{{__('Close')}}</span>
                                                 @else
                                                  <span class="badge badge-pill badge-success">{{__('Start')}}</span>
                                                 @endif






                                        </td>
                                       
                                        <td>
                                            <div class="actions">
                                               
                                                @if($contract->status != 'close') 
                                                @if (Auth::user()->type == 'owner')
                                                <a href="#" class="action-item px-2"  data-url="{{route('contracts.copy',$contract->id)}}"  data-ajax-popup="true" data-size="lg"  data-title="{{__('Duplicate')}}" data-toggle="tooltip" data-original-title="{{__('Duplicate')}}">
                                                    <i class="fas fa-copy"></i>
                                                </a>
                                                @endif
                                                 @endif


                                                <a href="{{ route('contractclient.show', $contract->id) }}" class="action-item px-2"   data-title="{{__('Show')}}" data-toggle="tooltip" data-original-title="{{__('Show')}}">
                                                    <i class="fas fa-eye"></i>
                                                </a>  

                                                @if (Auth::user()->type == 'owner')
                                                <a href="#" class="action-item px-2" data-url="{{ route('contractclient.edit',$contract->id) }}" data-ajax-popup="true" data-size="lg" data-title="{{__('Edit')}}" data-toggle="tooltip" data-original-title="{{__('Edit')}}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endif
                                                  
                                                @if (Auth::user()->type == 'owner')
                                                    <a href="#" class="action-item text-danger px-2" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?')}}|{{__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-tax-{{$contract->id}}').submit();">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                            </div>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['contractclient.destroy',$contract->id],'id'=>'delete-tax-'.$contract->id]) !!}
                                                {!! Form::close() !!}
                                                 @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script type="text/javascript">
        $(document).on('change', '.client_id', function() {

            var client_id=$(this).val();
            var data = {
                'client_id': client_id,
                _token: $('meta[name="csrf-token"]').attr('content')
                
            }
            $.ajax({
                type: "POST",
                url: "{{ route('project.by.user.id')}}",
                data: data,
                success: function(response) {
                    $('.project').html('');
                    $.each(response, function(index, value) {
                        
                       var html= '<option value="'+index+'">'+value+'</option>';
                           
                            $('.project').append(html);
                        });
              
                }
            });
           
        });

    </script>   





@endpush
