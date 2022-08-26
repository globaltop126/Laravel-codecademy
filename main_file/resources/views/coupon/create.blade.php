{{ Form::open(array('url' => 'coupons','method' =>'post')) }}
<div class="row">
    <div class="form-group col-md-12">
        {{Form::label('name',__('Name'),['class'=>'form-control-label'])}}
        {{Form::text('name',null,array('class'=>'form-control font-style','required'=>'required'))}}
    </div>

    <div class="form-group col-md-6">
        {{Form::label('discount',__('Discount'),['class'=>'form-control-label'])}}
        {{Form::number('discount',null,array('class'=>'form-control','required'=>'required','min'=>'1','max'=>'100','step'=>'0.01'))}}
        <span class="small">{{__('Note: Discount in Percentage')}}</span>
    </div>
    <div class="form-group col-md-6">
        {{Form::label('limit',__('Limit'),['class'=>'form-control-label'])}}
        {{Form::number('limit',null,array('class'=>'form-control','min'=>'1','required'=>'required'))}}
    </div>

    <div class="form-group col-md-12">
        {{Form::label('code',__('Code'),['class'=>'form-control-label'])}}
        <div class="selectgroup selectgroup-pills">
            <label class="selectgroup-item form-control-label">
                <input type="radio" name="icon-input" value="manual" class="selectgroup-input code" checked="checked">
                <span class="selectgroup-button selectgroup-button-icon">{{__('Manual')}}</span>
            </label>
            <label class="selectgroup-item form-control-label">
                <input type="radio" name="icon-input" value="auto" class="selectgroup-input code">
                <span class="selectgroup-button selectgroup-button-icon">{{__('Auto Generate')}}</span>
            </label>
        </div>
    </div>
    <div class="form-group col-md-12 d-block" id="manual">
        <input class="form-control font-uppercase" name="manualCode" type="text">
    </div>
    <div class="form-group col-md-12 d-none" id="auto">
        <div class="row">
            <div class="col-md-10">
                <input class="form-control" name="autoCode" type="text" id="auto-code">
            </div>
            <div class="col-md-2">
                <a href="#" class="btn btn-primary" id="code-generate"><i class="fas fa-history"></i></a>
            </div>
        </div>
    </div>
    <div class="form-group col-md-12 text-right">
        {{Form::submit(__('Create'),array('class'=>'btn btn-sm btn-primary rounded-pill'))}}
    </div>
</div>
{{ Form::close() }}

