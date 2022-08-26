{{ Form::model($expense, ['route' => ['projects.expenses.update',[$project->id,$expense->id]], 'id' => 'edit_expense', 'method' => 'POST','enctype' => 'multipart/form-data']) }}
<div class="row">
    <div class="col-12 col-md-12">
        <div class="form-group">
            {{ Form::label('name', __('Name'),['class' => 'form-control-label']) }}
            {{ Form::text('name', null, ['class' => 'form-control','required'=>'required']) }}
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="form-group">
            {{ Form::label('date', __('Date'),['class' => 'form-control-label']) }}
            {{ Form::date('date', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="form-group">
            {{ Form::label('amount', __('Amount'),['class' => 'form-control-label']) }}
            <div class="input-group input-group-merge">
                <div class="input-group-prepend">
                    <span class="input-group-text">{{ $project->currency }}</span>
                </div>
                {{ Form::number('amount', null, ['class' => 'form-control','required' => 'required','min' => '0']) }}
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="form-group">
            {{ Form::label('task_id', __('Task'),['class' => 'form-control-label']) }}
            <select class="form-control" name="task_id" id="task_id">
                <option value="0"></option>
                @foreach($project->tasks as $task)
                    <option value="{{ $task->id }}" {{ ($task->id == $expense->task_id) ? 'selected' : '' }}>{{ $task->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-12 col-md-12">
        <div class="form-group">
            {{ Form::label('description', __('Description'),['class' => 'form-control-label']) }}
            <small class="form-text text-muted mb-2 mt-0">{{__('This textarea will autosize while you type')}}</small>
            {{ Form::textarea('description', null, ['class' => 'form-control','rows' => '1','data-toggle' => 'autosize']) }}
        </div>
    </div>
    <div class="col-12 col-md-12">
        <div class="form-group">
            {{ Form::label('attachment', __('Attachment'),['class' => 'form-control-label']) }}
            <input type="file" name="attachment" id="attachment" class="custom-input-file"/>
            <label for="attachment">
                <i class="fa fa-upload"></i>
                <span>{{__('Choose a file…')}}</span>
            </label>
        </div>
    </div>
</div>

<div class="text-right">
    {{ Form::button(__('Update'), ['type' => 'submit','class' => 'btn btn-sm btn-primary rounded-pill']) }}
</div>
{{ Form::close() }}
