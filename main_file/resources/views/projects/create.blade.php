{{ Form::open(['url' => 'projects','id' => 'create_project','enctype' => 'multipart/form-data']) }}
<div class="row">
    <div class="col-12 col-md-12">
        <div class="form-group">
            {{ Form::label('name', __('Project name'),['class' => 'form-control-label']) }}
            {{ Form::text('name', null, ['class' => 'form-control','required'=>'required']) }}
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
            {{ Form::label('image', __('Project Image'),['class' => 'form-control-label']) }}
            <input type="file" name="image" id="image" class="custom-input-file" accept="image/*" data-filename="edit-product-image" onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])" />
            
            <label for="image">
                <i class="fa fa-upload"></i>
                <span>{{__('Choose a file…')}}</span>
            </label>
            
            <img src="" id="blah"  class="rounded-circle p-4"  /> 
            
        </div>
    </div> 
    {{-- <div class="col-12 col-md-12">
        <div class="form-group" id="product-image">
            <img class="profile-image rounded-circle-product" > 
        </div>
    </div> --}}

 
    <div class="col-12 col-md-4">
        <div class="form-group">
            {{ Form::label('estimated_hrs', __('Estimated Hours'),['class' => 'form-control-label']) }}
            {{ Form::number('estimated_hrs', null, ['class' => 'form-control','min'=>'0','maxlength' => '8']) }}
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="form-group">
            {{ Form::label('budget', __('Budget'),['class' => 'form-control-label']) }}
            {{ Form::number('budget', null, ['class' => 'form-control','step' =>'0.01']) }}
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="form-group">
            {{ Form::label('currency', __('Currency Symbol'),['class' => 'form-control-label']) }}
            {{ Form::text('currency', '$', ['class' => 'form-control','required' => 'required']) }}
        </div>
    </div>
    <div class="col-12 col-md-12">
        <div class="form-group">
            {{ Form::label('descriptions', __('Description'),['class' => 'form-control-label']) }}
            <small class="form-text text-muted mb-2 mt-0">{{__('This textarea will autosize while you type')}}</small>
            {{ Form::textarea('descriptions', null, ['class' => 'form-control','rows' => '1','data-toggle' => 'autosize']) }}
        </div>
    </div>
    <div class="col-12 col-md-12">
        <div class="form-group">
            {{ Form::label('tags', __('Tags'),['class' => 'form-control-label']) }}
            <small class="form-text text-muted mb-2 mt-0">{{ __('Seprated By Comma') }}</small>
            {{ Form::text('tags', null, ['class' => 'form-control','data-toggle' => 'tags','placeholder'=>__('Type here..')]) }}
        </div>
    </div>
</div>

<div class="text-right">
    {{ Form::button(__('Save'), ['type' => 'submit','class' => 'btn btn-sm btn-primary rounded-pill']) }}
    <button type="button" class="btn btn-sm btn-secondary rounded-pill" data-dismiss="modal">{{__('Cancel')}}</button>
</div>
{{ Form::close() }}
