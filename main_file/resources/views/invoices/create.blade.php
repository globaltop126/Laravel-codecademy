{{ Form::open(array('url' => 'invoices')) }}
<div class="row">
    <div class="col-12 col-md-6">
        <div class="form-group">
            {{ Form::label('project_id', __('Project'),['class' => 'form-control-label']) }}
            <select class="form-control" name="project_id" id="project_id" required>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
            <small>{{__('You can make invoice on your own project.')}}</small>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="form-group">
            {{ Form::label('client_id', __('Client'),['class' => 'form-control-label']) }}
            <select class="form-control" name="client_id" id="client_id" required>
            </select>
            <a href="{{route('users')}}" class="d-none" id="no_client"><small>{{__('Click here to create Client')}}</small></a>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="form-group">
            {{ Form::label('due_date', __('Due Date'),['class' => 'form-control-label']) }}
            {{ Form::date('due_date', null, ['class' => 'form-control','required'=>'required']) }}
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="form-group">
            {{ Form::label('tax_id', __('Tax %'),['class' => 'form-control-label']) }}
            {{ Form::select('tax_id',\Auth::user()->taxes->pluck('name','id'), null, ['class' => 'form-control','required'=>'required']) }}
            @if(\Auth::user()->taxes->count() == 0)
                <a href="{{route('settings')}}" id="no_tax"><small>{{__('Click here to create Tax')}}</small></a>
            @endif
        </div>
    </div>
</div>

<div class="text-right">
    {{ Form::button(__('Create'), ['type' => 'submit','class' => 'btn btn-sm btn-primary rounded-pill']) }}
    <button type="button" class="btn btn-sm btn-secondary rounded-pill" data-dismiss="modal">{{__('Cancel')}}</button>
</div>
{{ Form::close() }}

<script>
    $(document).ready(function () {
        $("select[name=project_id]").trigger('change');
    });
    $(document).on("change", "select[name=project_id]", function () {
        fillClient($(this).val());
    });
</script>
