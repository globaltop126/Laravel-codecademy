{{ Form::open(array('url' => 'email_template','method' =>'post')) }}
<div class="row">
    <div class="form-group col-md-12">
        {{Form::label('name',__('Name'))}}
        {{Form::text('name',null,array('class'=>'form-control font-style','required'=>'required'))}}
    </div>
    <div class="form-group col-12">
        {{ Form::label('keyword', __('Keyword'),['class' => 'form-control-label']) }}
        <small class="form-text text-muted mb-2 mt-0">{{ __('Seprated By Comma') }}</small>
        {{ Form::text('keyword', null, ['class' => 'form-control','data-toggle' => 'tags','placeholder' => __('Type here...'),]) }}
    </div>
    <div class="form-group col-md-12 text-right">
        {{Form::submit(__('Create'),array('class'=>'btn btn-sm btn-primary rounded-pill'))}}
    </div>
</div>
{{ Form::close() }}
