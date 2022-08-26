{{ Form::open(array('route' => array('invoice.custom.mail',$invoice_id))) }}
<div class="row">
    <div class="form-group col-md-12">
        {{ Form::label('email', __('Email'),['class'=>'form-control-label']) }}
        {{ Form::text('email', '', array('class' => 'form-control','required'=>'required')) }}
    </div>
</div>
<div class="modal-footer">
    {{Form::submit(__('Send'),array('class'=>'btn btn-primary btn-sm rounded-pill'))}}
    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-dismiss="modal">{{__('Cancel')}}</button>
</div>
{{ Form::close() }}
