@extends('layouts.admin')

@section('title')
    {{$project->name.__("'s Timesheet")}}
@endsection

@section('action-button')
    <a href="{{ route('projects.show',$project->id) }}" class="btn btn-sm btn-white rounded-circle btn-icon-only ml-2">
        <span class="btn-inner--icon"><i class="fas fa-arrow-left"></i></span>
    </a>
@endsection
@php
    $permissions = \Auth::user()->getPermission($project->id);
@endphp

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6 weekly-dates-div pt-1">
                            <a href="#" class="action-item previous"><i class="fas fa-arrow-left"></i></a>
                            <span class="weekly-dates"></span>
                            <input type="hidden" id="weeknumber" value="0">
                            <input type="hidden" id="selected_dates">
                            <a href="#" class="action-item next"><i class="fas fa-arrow-right"></i></a>
                        </div>
                        @if(isset($permissions) && in_array('show as admin timesheet',$permissions))
                            <div class="col text-right pt-1">
                                <div class="custom-control custom-checkbox timesheet-owner">
                                    <input type="checkbox" class="custom-control-input owner-timesheet-status" id="owner-check">
                                    <label class="custom-control-label" for="owner-check">{{__('See as Owner')}}</label>
                                </div>
                            </div>
                        @endif
                        @if(isset($permissions) && in_array('create timesheet',$permissions))
                            <div class="col text-right project_tasks_select">
                                <div class="dropdown btn btn-sm p-0">
                                    <a class="btn btn-xs btn-primary text-white" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        <i class="fas fa-plus mr-2"></i>{{__('Add Task on Timesheet')}}
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right tasks-box" x-placement="bottom-end">
                                        <div class="scrollbar-inner">
                                            <div class="mh-280">
                                                <div class="tasks-list"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-wrapper project-timesheet overflow-auto"></div>
                <div class="text-center notfound-timesheet">
                    <div class="empty-project-text text-center p-3 min-h-300">
                        <h5 class="pt-5">{{ __("We couldn't find any data") }}</h5>
                        <p class="m-0">{{ __("Sorry we can't find any timesheet records on this week.") }}</p>
                        <p class="m-0">{{ __("To add timesheet record go to Add Task on Timesheet") }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function ajaxFilterTimesheetTableView() {
            var mainEle = $('.project-timesheet');
            var notfound = $('.notfound-timesheet');
            var week = parseInt($('#weeknumber').val());
            var project_id = '{{ $project->id }}';
            var isowner = $('.owner-timesheet-status').prop('checked');
            var data = {
                week: week,
                project_id: project_id,
            }

            if (isowner) {
                data.isowner = isowner;
                $('.project_tasks_select').hide();
            } else {
                $('.project_tasks_select').show();
            }

            $.ajax({
                url: '{{ route('filter.timesheet.table.view') }}',
                data: data,
                success: function (data) {

                    // console.log(data);
                    $('.weekly-dates-div .weekly-dates').text(data.onewWeekDate);
                    $('.weekly-dates-div #selected_dates').val(data.selectedDate);

                    $('.project_tasks_select .tasks-list .dropdown-item').remove();

                    $.each(data.sectiontasks, function (i, item) {

                        var optionhtml = '';

                        if (item.section_id != 0 && item.section_name != '' && item.tasks.length > 0) {
                            optionhtml += `<a href="#" class="dropdown-item select-sub-heading" data-tasks-count="` + item.tasks.length + `">` + item.section_name + `</a>`;
                        }
                        $.each(item.tasks, function (ji, jitem) {
                            optionhtml += `<a href="#" class="dropdown-item select-task" data-task-id="` + jitem.task_id + `">` + jitem.task_name + `</a>`;
                        });
                        $('.project_tasks_select .tasks-list').append(optionhtml);
                    });

                    if (data.totalrecords == 0) {
                        mainEle.hide();
                        notfound.css('display', 'block');
                    } else {
                        notfound.hide();
                        mainEle.show();
                    }
                    mainEle.html(data.html);
                }
            });
        }

        $(function () {
            ajaxFilterTimesheetTableView();
        });

        $(document).on('click', '.weekly-dates-div .action-item', function () {
            var weeknumber = parseInt($('#weeknumber').val());
            if ($(this).hasClass('previous')) {
                weeknumber--;
                $('#weeknumber').val(weeknumber);
            } else if ($(this).hasClass('next')) {
                weeknumber++;
                $('#weeknumber').val(weeknumber);
            }
            ajaxFilterTimesheetTableView();
        });

        $(document).on('click', '[data-ajax-timesheet-popup="true"]', function (e) {
            e.preventDefault();

            var data = {};
            var url = $(this).data('url');
            var type = $(this).data('type');
            var date = $(this).data('date');
            var task_id = $(this).data('task-id');
            var user_id = $(this).data('user-id');

            data.date = date;
            data.task_id = task_id;

            if (user_id != undefined) {
                data.user_id = user_id;
            }
            if (type == 'create') {
                var title = '{{ __("Create Timesheet") }}';
                data.project_id = '{{ $project->id }}';
            } else if (type == 'edit') {
                var title = '{{ __("Edit Timesheet") }}';
            }

            $("#commonModal .modal-title").html(title + ` <small>(` + moment(date).format("ddd, Do MMM YYYY") + `)</small>`);
            $.ajax({
                url: url,
                data: data,
                dataType: 'html',
                success: function (data) {
                    $('#commonModal .modal-body').html(data);
                    if ($('#date').length > 0) {
                        $('#date').daterangepicker({
                            singleDatePicker: true,
                            locale: {
                                format: 'YYYY-MM-DD'
                            }
                        });
                    }

                    $('#commonModal').modal({backdrop: 'static', keyboard: false});
                }
            });
        });

        $('.project_tasks_select .tasks-box').on('click', '.select-task', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var mainEle = $('.project-timesheet');
            var notfound = $('.notfound-timesheet');

            var task_id = $(this).attr('data-task-id');
            var selected_dates = $('#selected_dates').val();

            $.ajax({
                url: '{{route('append.timesheet.task.html')}}',
                data: {
                    project_id: '{{ $project->id }}',
                    task_id: task_id,
                    selected_dates: selected_dates,
                },
                success: function (data) {
                    notfound.hide();
                    mainEle.show();
                    $('.project-timesheet tbody').append(data.html);
                    $('.project_tasks_select .tasks-list .select-task[data-task-id="' + task_id + '"]').remove();
                }
            });
        });

        $(document).on('change', '#time_hour, #time_minute', function () {

            var hour = $('#time_hour').children("option:selected").val();
            var minute = $('#time_minute').children("option:selected").val();
            var total = $('#totaltasktime').val().split(':');

            if (hour == '00' && minute == '00') {
                $(this).val('');
                return;
            }

            hour = hour != '' ? hour : 0;
            hour = parseInt(hour) + parseInt(total[0]);

            minute = minute != '' ? minute : 0;
            minute = parseInt(minute) + parseInt(total[1]);

            if (minute > 50) {
                minute = minute - 60;
                hour++;
            }

            hour = hour < 10 ? '0' + hour : hour;
            minute = minute < 10 ? '0' + minute : minute;

            $('.display-total-time small').text('{{ __("Total Time worked on this task") }} : ' + hour + ' {{ __("Hours") }} ' + minute + ' {{ __("Minutes") }}');
        });

        @if(isset($permissions) && in_array('show as admin timesheet',$permissions))
        $(document).on('click', '.timesheet-owner .owner-timesheet-status', function (e) {
            ajaxFilterTimesheetTableView();
        });
        @endif
    </script>
@endpush
