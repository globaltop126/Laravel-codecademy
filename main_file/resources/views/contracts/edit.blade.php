
    {{-- {{ dd('hiiii') }} --}}
  
    {{ Form::model($contract, array('route' => array('contractclient.update', [$contract->id]), 'method' => 'PUT')) }}   
    
        <div class="row">

            <div class="col-md-6 form-group">
                {{ Form::label('client_name', __('Client Name'),['class'=>'col-form-label']) }}
                {{ Form::select('client_name', $client,$contract->client, array('class' => 'form-control select2','required'=>'required')) }}
            </div>
           
            <div class="col-md-6 form-group">
                {{ Form::label('project', __('Project'),['class'=>'col-form-label']) }}
                {{  Form::select('project', $project,null, array('class' => 'form-control select2','required'=>'required'))  }}
            </div>


                <div class="col-md-6">
                    <div class="form-group">
                        {{ Form::label('subject', __('Subject'), ['class' => 'col-form-label']) }}
                        {{ Form::text('subject', null, ['class' => 'form-control ', 'required' => 'required']) }}
                    </div>
                </div> 

            <div class="col-md-6 form-group">
                {{ Form::label('value', __('Value'),['class'=>'col-form-label']) }}
                {{ Form::number('value', null, array('class' => 'form-control','required'=>'required','min' => '1')) }}
            </div>
            
            <div class="col-md-12 form-group">
                {{ Form::label('type', __('Type'),['class'=>'col-form-label']) }}
                {{ Form::select('type', $contractType,null, array('class' => 'form-control select2','required'=>'required')) }}
            </div>
            
            <div class="form-group col-md-6">
                {{ Form::label('start_date', __('Start Date'),['class' => 'col-form-label']) }}            
                {{ Form::date('start_date', null, array('class' => 'form-control')) }}
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('end_date', __('End Date'),['class' => 'col-form-label']) }}
                {{ Form::date('end_date', null, array('class' => 'form-control')) }}
            </div>
            <div class="col-md-12 form-group">
                {{ Form::label('notes', __('Description'),['class'=>'col-form-label']) }}
                {{ Form::textarea('notes', null, array('class' => 'form-control')) }}
            </div>
            <div class="col-md-12 mb-2">
                <label class="col-form-label">{{__('Status')}}</label>
                <div class="d-flex radio-check">
                    <div class="custom-control custom-radio custom-control-inline m-1" >
                        <input  type="radio" id="start" name="status" value="start" class="form-check-input" {{ ($contract->status == 'start') ? 'checked' : '' }} />
                        <label class="form-check-labe" for="start">{{__('Start')}}</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline m-1">
                        <input type="radio" id="close" name="status" value="close" class="form-check-input" {{ ($contract->status == 'close') ? 'checked' : '' }} />
                        <label class="form-check-labe" for="close">{{__('Close')}}</label>
                    </div>
                </div>
            </div>
            
        </div>
   
    {{-- <div class="modal-footer">
        <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
        <button type="submit" class="btn  btn-primary">{{__('Update')}}</button>
       
    </div> --}}

    <div class="text-right pt-3">
        {{ Form::button(__('Update'), ['type' => 'submit','class' => 'btn btn-sm btn-primary rounded-pill']) }}
        <button type="button" class="btn btn-sm btn-secondary rounded-pill" data-dismiss="modal">{{__('Cancel')}}</button>
    </div>

        {{ Form::close() }}
    
    <script>
        $('.sdf').trigger('change');
    </script>