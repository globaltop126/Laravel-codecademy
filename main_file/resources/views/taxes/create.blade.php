{{ Form::open(array('url' => 'taxes')) }}
<div class="row">
    <div class="col-12 col-md-6">
        <div class="form-group">
            {{ Form::label('name', __('Name'),['class' => 'form-control-label']) }}
            {{ Form::text('name', null, ['class' => 'form-control','required'=>'required']) }}
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="form-group">
            {{ Form::label('rate', __('Rate %'),['class' => 'form-control-label']) }}
            {{ Form::number('rate', null, ['class' => 'form-control','required'=>'required','min'=>'0','max'=>'100','step'=>'0.01']) }}
        </div>
    </div>
</div>

<div class="text-right pt-3">
    {{ Form::button(__('Create'), ['type' => 'submit','class' => 'btn btn-sm btn-primary rounded-pill']) }}
    <button type="button" class="btn btn-sm btn-secondary rounded-pill" data-dismiss="modal">{{__('Cancel')}}</button>
</div>
{{ Form::close() }}
