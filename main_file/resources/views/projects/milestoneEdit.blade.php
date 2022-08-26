{{ Form::model($milestone, array('route' => array('project.milestone.update', $milestone->id), 'method' => 'POST')) }}
<div class="row">
    <div class="form-group col-md-6">
        {{ Form::label('title', __('Title'),['class' => 'form-control-label']) }}
        {{ Form::text('title', null, array('class' => 'form-control','required'=>'required')) }}
        @error('title')
        <span class="invalid-title" role="alert">
            <strong class="text-danger">{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="form-group  col-md-6">
        {{ Form::label('status', __('Status'),['class' => 'form-control-label']) }}
        {!! Form::select('status',\App\Models\Project::$status, null,array('class' => 'form-control selectric','required'=>'required')) !!}
        @error('client')
        <span class="invalid-client" role="alert">
            <strong class="text-danger">{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>
     <div class="row">
        <div class="col-md-6">
           <div class="form-group ">
               <label class="form-control-label">{{ __('Start Date') }}</label>
            <div class="input-group date ">
                <input class="form-control" type="date" id="start_date" name="start_date" value="{{$milestone->start_date}}" autocomplete="off">
                
            </div>
            </div>
        </div>
        <div class="col-md-6">
              <div class="form-group">
                   <label class="form-control-label">{{ __('End Date') }}</label>   
                <div class="input-group date ">
               <input class="form-control" type="date" id="end_date" name="end_date" value="{{$milestone->end_date}}" autocomplete="off" >
               
                </div>
            </div>
        </div>
    </div>
<div class="row">
    <div class="form-group  col-md-12">
        {{ Form::label('description', __('Description'),['class' => 'form-control-label']) }}
        {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'2']) !!}
        @error('description')
        <span class="invalid-description" role="alert">
            <strong class="text-danger">{{ $message }}</strong>
        </span>
        @enderror
    </div>

       <div class="col-md-12">
            <div class="form-group">
                  <label for="task-summary" class="form-control-label">{{ __('Progress')}}</label>
                <input type="range" class="slider w-100 mb-0 " name="progress" id="myRange" value="{{($milestone->progress)?$milestone->progress:'0'}}" min="0" max="100" oninput="ageOutputId.value = myRange.value">
                <output name="ageOutputName" id="ageOutputId">{{($milestone->progress)?$milestone->progress:"0"}}</output>
                %
            </div>
        </div>
    <div class="col-md-12 text-right">
        {{ Form::button(__('Update'), ['type' => 'submit','class' => 'btn btn-sm btn-primary rounded-pill']) }}
        <button type="button" class="btn btn-sm btn-secondary rounded-pill" data-dismiss="modal">{{__('Cancel')}}</button>
    </div>
</div>
{{ Form::close() }}
