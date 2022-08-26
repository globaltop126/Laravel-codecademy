@extends('layouts.invoicepayheader')



@section('content')

    <style>
        #sdnfsjdfn {
            text-align: left;
            /* width: 800px !important; */
        }
    </style>

    <div class="row">

        <div class="col-lg-12">
            <div class="container">


                {{-- <div class="card " id="printTable" style="margin-left: 180px;margin-right: -57px;"> --}}
                <div class="card " id="printTable" >


                    <div class="card-body view" id="boxes" style="height:auto !important;">

                        <div class="img-view">

                            <div class="col-8">
                                <img src="{{ $img }}" style="filter: drop-shadow(2px 4px 6px black)">
                            </div>

                            <div class="col-lg-12 col-md-4 mt-3">
                                <h6 class="d-inline-block m-0 d-print-none">{{ __('Contract :') }}
                                </h6>
                                <span class="col-4"><span
                                        class="text-md"><p>{{ App\Models\Utility::contractNumberFormat($contract->id) }}</p></span></span>
                            </div>
                        </div>

                        <div class="row align-items-center mb-4">

                            <div class="col-sm-12 mb-3 mb-sm-0">
                                <div class="row  mt-3">
                                    <div class="col-8">
                                        <div class="col-lg-12 col-md-8">
                                            <h6 class="d-inline-block m-0 d-print-none">{{ __('Type :') }}
                                            </h6>
                                            <span class="col-md-8"><span
                                                    class="text-md"><p>{{ $contract->ContractType->name }}</p></span></span>
                                        </div>

                                        <div class="col-lg-12 col-md-8 mt-3">
                                            <h6 class="d-inline-block m-0 d-print-none">{{ __('Value  :') }}
                                            </h6>
                                            <span class="col-md-8"><span
                                                    class="text-md"><p>{{ Auth::user()->priceFormat($contract->value) }}</p></span></span>
                                        </div>
                                    </div>


                                    <div class="col-4">
                                        <div class="col-12 text-right">
                                            <h6 class="d-inline-block m-0 d-print-none">{{ __('Start Date   :') }}</h6>
                                            <span class="col-md-8"><span
                                                    class="text-md"><p>{{ Auth::user()->dateFormat($contract->start_date) }}</p></span></span>
                                        </div>
                                        <div class="col-12 text-right">
                                            <h6 class="d-inline-block m-0 d-print-none">{{ __('End Date   :') }}</h6>
                                            <span class="col-md-8"><span
                                                    class="text-md"><p>{{ Auth::user()->dateFormat($contract->end_date) }}</p></span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">

                                    <p>{!! $contract->notes !!}</p>

                                    
                                    <div id="sdnfsjdfn">
                                        <p>{!! $contract->contract_description !!}</p>
                                    </div>

                                  
                                </div>


                                <div class="col-12 row d-flex">
                                    
                                    <div class="col-8">
                                        <img src="{!! $contract->owner_signature !!}">
                                        <h6>{{ __('Owner Signature') }}</h6>
                                    </div>
                                    <div class="col-4 text-end">
                                        <img src="{!! $contract->client_signature !!}">
                                        <h6>{{ __('Client Signature') }}<h6>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>

    <?php $url = route('contractclient.show', $contract->id); ?>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="{{ asset('/js/html2pdf.bundle.min.js') }}"></script>
    <script>
        function closeScript() {
            setTimeout(function() {
                window.location.href = '{{ $url }}';
            }, 1000);
        }

        $(window).on('load', function() {
            var element = document.getElementById('boxes');
            var opt = {
                margin: 0.3,
                filename: '{{ App\Models\Utility::contractNumberFormat($contract->id) }}',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 4,
                    dpi: 72,
                    letterRendering: true
                },
                jsPDF: {
                    unit: 'in',
                    format: 'A4',
                    orientation: 'landscape'
                },
            };

            html2pdf().set(opt).from(element).save().then(closeScript);
        });
    </script>
