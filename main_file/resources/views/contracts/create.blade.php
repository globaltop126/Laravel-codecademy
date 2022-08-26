{{ Form::open(array('url' => 'contractclient','method' =>'post')) }}
<div class="row">
    <div class="col-md-6 form-group">
        {{ Form::label('client_name', __('Client Name'), ['class' => 'col-form-label']) }}
        {{ Form::select('client_name', $client, null, ['class' => 'form-control client_id','id' => 'client_id', 'placeholder' => 'Select client' ,'required' => 'required']) }}
    </div>  
    <div class="col-md-6 form-group">
        {{ Form::label('project', __('Project'), ['class' => 'col-form-label']) }}
        <div class="project-div">
            <select class="form-control select2 project" id="project" name="project" >
                <option value=''> {{ __('Select Project') }} </option>
            </select>
        </div>
    </div>
    <div class="col-md-6 form-group">
        {{ Form::label('value', __('Value'), ['class' => 'col-form-label']) }}
        {{ Form::number('value', '', ['class' => 'form-control', 'required' => 'required', 'min' => '1']) }}
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('subject', __('Subject'), ['class' => 'col-form-label']) }}
            {{ Form::text('subject', null, ['class' => 'form-control ', 'required' => 'required']) }}
        </div>
    </div> 

    <div class="col-md-12">
        <div class="form-group">
            {{ Form::label('type', __('Type'), ['class' => 'col-form-label']) }}
            {{ Form::select('type', $contractType, null, ['class' => 'form-control select2', 'required' => 'required']) }}
        </div>
    </div> 

    <div class="col-12 col-md-6">
        <div class="form-group">
            {{ Form::label('start_date', __('Start date'),['class' => 'form-control-label']) }}
            {{ Form::date('start_date', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="form-group">
            {{ Form::label('end_date', __('End date'),['class' => 'form-control-label']) }}
            {{ Form::date('end_date', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-12 col-md-12">
        <div class="form-group">
            {{ Form::label('descriptions', __('Description'),['class' => 'form-control-label']) }}
            <small class="form-text text-muted mb-2 mt-0">{{__('This textarea will autosize while you type')}}</small>
            {{ Form::textarea('descriptions', null, ['class' => 'form-control','rows' => '1','data-toggle' => 'autosize']) }}
        </div>
    </div>

    <div class="col-md-12 form-group">
        <label class="col-form-label">{{ __('Status') }}</label>
        <div class="d-flex radio-check">
            <div class="custom-control custom-radio custom-control-inline m-1">
                <input type="radio" id="start" name="status" value="start" class="form-check-input" checked>
                <label class="form-check-labe" for="start">{{ __('Start') }}</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline m-1">
                <input type="radio" id="close" name="status" value="close" class="form-check-input">
                <label class="form-check-labe" for="close">{{ __('Close') }}</label>
            </div>
        </div>
    </div>


    <div class="form-group col-md-12 text-right">
        {{Form::submit(__('Create'),array('class'=>'btn btn-sm btn-primary rounded-pill'))}}
        <button type="button" class="btn btn-sm btn-secondary rounded-pill" data-dismiss="modal">{{__('Cancel')}}</button>
    </div>
</div>
{{ Form::close() }}

