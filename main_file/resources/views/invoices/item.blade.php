{{ Form::model($invoice, array('route' => array('invoices.products.store', $invoice->id), 'method' => 'POST')) }}
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <input type="text" class="form-control font-style" value="{{(!empty($invoice->project)?$invoice->project->name:'')}}" readonly>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <ul class="nav nav-tabs nav-overflow" role="tablist">
            <li class="nav-item">
                <a href="#tasks" id="tasks-tab" class="nav-link items_tab active" data-toggle="tab" role="tab" aria-selected="true">
                    {{__('Tasks')}}
                </a>
            </li>
            <li class="nav-item">
                <a href="#others" id="others-tab" class="nav-link items_tab" data-toggle="tab" role="tab" aria-selected="true">
                    {{__('Others')}}
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">
                <div class="form-group">
                    <label for="task" class="form-control-label">{{__('Task')}}</label>
                    <select name="task" id="task" class="form-control">
                        @foreach($tasks as $task)
                            <option value="{{$task}}">{{$task}}</option>
                        @endforeach
                    </select>
                    @if(count($tasks) == 0)
                        <a href="{{ route('projects.tasks.index',$invoice->project_id) }}" class="d-none" id="no_client"><small>{{__('Click here to create Task')}}</small></a>
                    @endif
                </div>
            </div>
            <div class="tab-pane fade show" id="others" role="tabpanel" aria-labelledby="others-tab">
                <div class="form-group">
                    <label for="other" class="form-control-label">{{__('Title')}}</label>
                    <input type="text" class="form-control font-style" name="title">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="price" class="form-control-label">{{__('Price')}}</label>
            <input type="number" class="form-control font-style" name="price" step="0.01" required>
            <input type="hidden" id="from" name="from" value="tasks-tab">
        </div>
    </div>
    <div class="col-md-12 text-right">
        {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill'))}}
        <button type="button" class="btn btn-sm btn-secondary rounded-pill" data-dismiss="modal">{{__('Cancel')}}</button>
    </div>
    {{ Form::close() }}
</div>

