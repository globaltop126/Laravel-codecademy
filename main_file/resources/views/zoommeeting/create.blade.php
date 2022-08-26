{{ Form::open(['route' => 'zoommeeting.store', 'id' => 'store-user', 'method' => 'post']) }}
<div class="row">
    <div class="form-group col-md-12">
        {{ Form::label('title', __('Topic')) }}
        {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => __('Enter Meeting Title'), 'required' => 'required']) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('projects', __('Projects')) }}
        {{ Form::select('project_id', $project, null, ['class' => 'form-control project_select project_id', 'placeholder' => __('Select Project')]) }}
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('employee', __('Employee')) }}
        {{ Form::select('employee[]', [], false, ['class' => 'form-control employee_select', 'data-toggle' => 'select2', 'multiple' => '', 'required' => 'required']) }}
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('datetime', __('Start Date / Time')) }}
        {{ Form::text('start_date', null, ['class' => 'form-control datepicker datetime_class_start_date', 'placeholder' => __('Select Date/Time'), 'required' => 'required']) }}
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('duration', __('Duration')) }}
        {{ Form::number('duration', null, ['class' => 'form-control', 'placeholder' => __('Enter Duration'), 'required' => 'required']) }}
    </div>

    <div class="form-group col-md-6">
        {{ Form::label('password', __('Password (Optional)')) }}
        {{ Form::password('password', ['class' => 'form-control', 'placeholder' => __('Enter Password')]) }}
    </div>
    <div class="form-group col-md-6">
        <div class="custom-control custom-checkbox pl-0">
            <input type="checkbox" name="client_id" id="client_id" class="client_view check_client">
            <label for="display">{{ __('Invite Client For Zoom Meeting') }}</label>
        </div>
        <div class="row">
            <div class="form-group col-md-12 client" style="display: none">
                {{ Form::label('client_id', __('Client')) }}
                {{ Form::select('client_id[]', [], false, ['class' => 'form-control client_select', 'data-toggle' => 'select2', 'multiple' => '']) }}
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button class="btn btn-sm btn-primary rounded-pill" type="submit" style="margin-bottom: 10px;"
        id="create-client">{{ __('Create') }}<span class="spinner" style="display: none;"><i
                class="fa fa-spinner fa-spin"></i></span>
    </button>
</div>
{{ Form::close() }}
<script type="text/javascript">
    // $(document).ready(function () {

    //     $('.date').daterangepicker({
    //         "singleDatePicker": true,
    //         "timePicker": true,
    //         "locale": {
    //             "format": 'MM/DD/YYYY H:mm'
    //         },
    //         "timePicker24Hour": true,
    //     }, function(start, end, label) {
    //     console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    //     });
    //     getProjects($('#client_id').val());
    // });

    function ddatetime_range() {
        $('.datetime_class_start_date').daterangepicker({
            "singleDatePicker": true,
            "timePicker": true,
            "autoApply": false,
            "locale": {
                "format": 'YYYY-MM-DD H:mm'
            },
            "timePicker24Hour": true,
        }, function(start, end, label) {
            $('.start_date').val(start.format('YYYY-MM-DD H:mm'));
        });
    }


    $(document).on('change', '.project_select', function() {
        var project_id = $(this).val();
        getparent(project_id);
    });

    function getparent(bid) {
        $.ajax({
            url: `{{ url('zoom/projects/select') }}/${bid}`,
            type: 'GET',
            success: function(data) {

                $('.employee_select').empty();
                $('.client_select').empty();
                $.each(data.users, function(i, item) {
                    $('.employee_select').append('<option value="' + item.id + '">' + item.name +
                        '</option>');
                });

                $.each(data.clients, function(i, item) {
                    $('.client_select').append('<option value="' + item.id + '">' + item.name +
                        '</option>');
                });
                if (data == '') {
                    $('.employee_select').empty();
                    $('.client_select').empty();
                }
            }
        });
    }
</script>

<script>
    $(document).on('change', '.check_client', function() {
        if ($('.client_view.check_client').is(':checked')) {
            $('.client').show();
        } else {
            $('.client').hide();
        }
    });
</script>
