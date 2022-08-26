@extends('layouts.admin')

@section('title')
    {{__('Invoice Print Setting')}}
@endsection

@section('action-button')
    <a href="{{ route('settings') }}" class="btn btn-sm btn-white rounded-circle btn-icon-only ml-0">
        <span class="btn-inner--icon"><i class="fas fa-arrow-left"></i></span>
    </a>
@endsection

@section('content')
    <div class="card card-body p-md-5">
        <div class="row">
            <div class="col-md-12 pb-5">
                <form id="setting-form" method="post" action="{{route('invoice.template.store')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 col-xs-12">
                            <div class="form-group">
                                <label for="address" class="form-control-label">{{__('Invoice Template')}}</label>
                                <select class="form-control" name="invoice_template">
                                    @foreach(Utility::templateData()['templates'] as $key => $template)
                                        <option value="{{$key}}" {{(isset($decoded['invoice_template']) && $decoded['invoice_template'] == $key) ? 'selected' : ''}}>{{$template}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="invoice_logo" class="form-control-label">{{__('Invoice Logo')}}</label>
                                <input type="file" name="invoice_logo" id="invoice_logo" class="custom-input-file" accept="image/*">
                                <label for="invoice_logo">
                                    <i class="fa fa-upload"></i>
                                    <span>{{__('Choose a file…')}}</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-12">
                            <div class="form-group">
                                <label class="form-control-label">{{__('Color Input')}}</label>
                                <div class="row gutters-xs">
                                    @foreach(Utility::templateData()['colors'] as $key => $color)
                                        <div class="col-auto">
                                            <label class="colorinput">
                                                <input name="invoice_color" type="radio" value="{{$color}}" class="colorinput-input" {{(isset($decoded['invoice_color']) && $decoded['invoice_color'] == $color) ? 'checked' : ''}}>
                                                <span class="colorinput-color" style="background: #{{$color}}"></span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-12 text-right">
                            <button class="btn btn-sm btn-primary rounded-pill">
                                {{__('Save')}}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-12">
                @if(isset($decoded['invoice_template']) && isset($decoded['invoice_color']))
                    <iframe id="invoice_frame" class="w-100 h-1050" frameborder="0" src="{{route('invoice.preview',[$decoded['invoice_template'],$decoded['invoice_color']])}}"></iframe>
                @else
                    <iframe id="invoice_frame" class="w-100 h-1050" frameborder="0" src="{{route('invoice.preview',['template1','ffffff'])}}"></iframe>
                @endif
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        $(document).on("change", "select[name='invoice_template'], input[name='invoice_color']", function () {
            var template = $("select[name='invoice_template']").val();
            var color = $("input[name='invoice_color']:checked").val();
            $('#invoice_frame').attr('src', '{{url('/invoices/preview')}}/' + template + '/' + color);
        });
    </script>
@endpush
