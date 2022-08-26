<form method="post" action="{{ route('projects.user.permission.store',[$project->id,$user->id]) }}">
    @csrf
    <table class="table align-items-center">
        <thead>
        <tr>
            <th>{{__('Module')}}</th>
            <th>{{__('Permissions')}}</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{{__('Milestone')}}</td>
            <td>
                <div class="row cust-checkbox-row">
                    <div class="col-2 custom-control custom-checkbox">
                        <input class="custom-control-input" id="permission3" @if(in_array('create milestone',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="create milestone">
                        <label for="permission3" class="custom-control-label pt-1">{{__('Create')}}</label><br>
                    </div>
                    <div class="col-2 custom-control custom-checkbox">
                        <input class="custom-control-input" id="permission4" @if(in_array('edit milestone',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="edit milestone">
                        <label for="permission4" class="custom-control-label pt-1">{{__('Edit')}}</label><br>
                    </div>
                    <div class="col-2 custom-control custom-checkbox">
                        <input class="custom-control-input" id="permission5" @if(in_array('delete milestone',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="delete milestone">
                        <label for="permission5" class="custom-control-label pt-1">{{__('Delete')}}</label><br>
                    </div>
                    <div class="col"></div>
                </div>
            </td>
        </tr>
        <tr>
            <td>{{__('Task')}}</td>
            <td>
                <div class="row cust-checkbox-row">
                    <div class="col-2 custom-control custom-checkbox">
                        <input class="custom-control-input" id="permission7" @if(in_array('create task',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="create task">
                        <label for="permission7" class="custom-control-label pt-1">{{__('Create')}}</label><br>
                    </div>
                    <div class="col-2 custom-control custom-checkbox">
                        <input class="custom-control-input" id="permission8" @if(in_array('edit task',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="edit task">
                        <label for="permission8" class="custom-control-label pt-1">{{__('Edit')}}</label><br>
                    </div>
                    <div class="col-2 custom-control custom-checkbox">
                        <input class="custom-control-input" id="permission9" @if(in_array('delete task',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="delete task">
                        <label for="permission9" class="custom-control-label pt-1">{{__('Delete')}}</label><br>
                    </div>
                    <div class="col-2 custom-control custom-checkbox">
                        <input class="custom-control-input" id="permission6" @if(in_array('show task',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="show task">
                        <label for="permission6" class="custom-control-label pt-1">{{__('Show')}}</label><br>
                    </div>
                    <div class="col-2 custom-control custom-checkbox">
                        <input class="custom-control-input" id="permission10" @if(in_array('move task',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="move task">
                        <label for="permission10" class="custom-control-label pt-1">{{__('Move')}}</label><br>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>{{__('Timesheet')}}</td>
            <td>
                <div class="row cust-checkbox-row">
                    <div class="col-2 custom-control custom-checkbox">
                        <input class="custom-control-input" id="permission16" @if(in_array('create timesheet',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="create timesheet">
                        <label for="permission16" class="custom-control-label pt-1">{{__('Create')}}</label><br>
                    </div>
                    <div class="col-2 custom-control custom-checkbox">
                        <input class="custom-control-input" id="permission17" @if(in_array('show as admin timesheet',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="show as admin timesheet">
                        <label for="permission17" class="custom-control-label pt-1">{{__('Show as Admin')}}</label><br>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>{{__('Expense')}}</td>
            <td>
                <div class="row cust-checkbox-row">
                    <div class="col-2 custom-control custom-checkbox">
                        <input class="custom-control-input" id="permission11" @if(in_array('create expense',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="create expense">
                        <label for="permission11" class="custom-control-label pt-1">{{__('Create')}}</label><br>
                    </div>
                    <div class="col-2 custom-control custom-checkbox">
                        <input class="custom-control-input" id="permission12" @if(in_array('show expense',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="show expense">
                        <label for="permission12" class="custom-control-label pt-1">{{__('Show')}}</label><br>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>{{__('Activity')}}</td>
            <td>
                <div class="row cust-checkbox-row">
                    <div class="col-2 custom-control custom-checkbox">
                        <input class="custom-control-input" id="permission1" @if(in_array('show activity',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="show activity">
                        <label for="permission1" class="custom-control-label pt-1">{{__('Show')}}</label><br>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>{{__('Setting')}}</td>
            <td>
                <div class="row cust-checkbox-row">
                    <div class="col-2 custom-control custom-checkbox">
                        <input class="custom-control-input" id="permission22" @if(in_array('project setting',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="project setting">
                        <label for="permission22" class="custom-control-label pt-1">{{__('Project Setting')}}</label><br>
                    </div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="form-group mt-3 text-right">
        <button class="btn btn-sm btn-primary rounded-pill" type="submit">{{ __('Update Permission') }}</button>
    </div>
</form>
