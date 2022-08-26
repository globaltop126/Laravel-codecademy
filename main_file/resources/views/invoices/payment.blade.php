{{ Form::model($invoice, array('route' => array('invoices.payments.store', $invoice->id), 'method' => 'POST')) }}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('amount', __('Amount'),['class'=>'form-control-label']) }}
            {{ Form::number('amount', $invoice->getDue(), array('class' => 'form-control','required'=>'required','min'=>'0',"step"=>"0.01")) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('date', __('Payment Date'),['class'=>'form-control-label']) }}
            {{ Form::date('date', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            {{ Form::label('notes', __('Notes'),['class'=>'form-control-label']) }}
            {{ Form::textarea('notes', null, array('class' => 'form-control','rows'=>'2')) }}
        </div>
    </div>
    <div class="col-md-12 text-right">
        {{Form::submit(__('Create'),array('class'=>'btn btn-sm btn-primary rounded-pill'))}}
        <button type="button" class="btn btn-sm btn-secondary rounded-pill" data-dismiss="modal">{{__('Cancel')}}</button>
    </div>
</div>
{{ Form::close() }}
