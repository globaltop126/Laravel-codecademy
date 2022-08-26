@extends('layouts.admin')

@section('title')
    {{$project->name.__("'s Tasks")}}
@endsection

@push('theme-script')
    <script src="{{ asset('assets/libs/dragula/dist/dragula.min.js') }}"></script>
@endpush

@section('action-button')
    <a href="{{ route('projects.show',$project->id) }}" class="btn btn-sm btn-white rounded-circle btn-icon-only ml-2">
        <span class="btn-inner--icon"><i class="fas fa-arrow-left"></i></span>
    </a>
@endsection

@php
    $permissions = \Auth::user()->getPermission($project->id);
@endphp

@section('content')
    <div class="card overflow-hidden">
        <div class="container-kanban">
            <div class="kanban-board min-750" @if(isset($permissions) && in_array('move task',$permissions)) data-plugin="dragula" @endif data-containers='{{json_encode($stageClass)}}'>
                @foreach($stages as $stage)
                    <div class="kanban-col px-0">
                        <div class="card-list card-list-flush">
                            <div class="card-list-title row align-items-center mb-3">
                                <div class="col">
                                    <h6 class="mb-0">{{ $stage->name }}</h6>
                                </div>
                                @if((isset($permissions) && in_array('create task',$permissions)))
                                    <div class="col text-right">
                                        <div class="actions">
                                            <a class="action-item mr-2" href="#" data-url="{{ route('projects.tasks.create',[$stage->project_id,$stage->id]) }}" data-ajax-popup="true" data-size="lg" data-title="{{__('Add Task in ').$stage->name}}">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="card-list-body task-list-items" id="task-list-{{ $stage->id }}" data-status="{{$stage->id}}">
                                @foreach($stage->tasks as $taskDetail)
                                    <div class="card card-progress @if(isset($permissions) && in_array('move task',$permissions)) draggable-item @endif border shadow-none" id="{{$taskDetail->id}}" style="{{ !empty($taskDetail->priority_color) ? 'border-left: 2px solid '.$taskDetail->priority_color.' !important' :'' }};">
                                        <div class="card-body">
                                            <div class="row align-items-center mb-2">
                                                <div class="col-6">
                                                    <span class="badge badge-pill badge-xs badge-{{\App\Models\ProjectTask::$priority_color[$taskDetail->priority]}}">{{ __(\App\Models\ProjectTask::$priority[$taskDetail->priority]) }}</span>
                                                </div>
                                                <div class="col-6 text-right">
                                                    @if(str_replace('%','',$taskDetail->taskProgress()['percentage']) > 0)<span class="text-sm">{{ $taskDetail->taskProgress()['percentage'] }}</span>@endif
                                                    @if(isset($permissions) && (in_array('show task',$permissions) || in_array('edit task',$permissions) || in_array('delete task',$permissions)))
                                                        <div class="dropdown action-item">
                                                            <a href="#" class="action-item" role="button" data-toggle="dropdown"><i class="fas fa-ellipsis-h"></i></a>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                @if(isset($permissions) && in_array('show task',$permissions))
                                                                    <a href="#" data-url="{{ route('projects.tasks.show',[$project->id,$taskDetail->id]) }}" data-ajax-popup-right="true" class="dropdown-item">{{ __('View') }}</a>
                                                                @endif
                                                                @if(isset($permissions) && in_array('edit task',$permissions))
                                                                    <a href="#" data-url="{{ route('projects.tasks.edit',[$project->id,$taskDetail->id]) }}" data-ajax-popup="true" data-size="lg" data-title="{{__('Edit ').$taskDetail->name}}" class="dropdown-item">{{ __('Edit') }}</a>
                                                                @endif
                                                                @if(isset($permissions) && in_array('delete task',$permissions))
                                                                    <a href="#" class="dropdown-item del_task" data-url="{{ route('projects.tasks.destroy',[$project->id,$taskDetail->id]) }}">{{ __('Delete') }}</a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @if(isset($permissions) && in_array('show task',$permissions))
                                                <a class="h6 task-name-break" href="#" data-url="{{ route('projects.tasks.show',[$project->id,$taskDetail->id]) }}" data-ajax-popup-right="true">{{ $taskDetail->name }}</a>
                                            @else
                                                <a class="h6 task-name-break" href="#">{{ $taskDetail->name }}</a>
                                            @endif
                                            <div class="row align-items-center">
                                                <div class="col-12">
                                                    <div class="actions d-inline-block">
                                                        @if(count($taskDetail->taskFiles) > 0)
                                                            <div class="action-item mr-2"><i class="fas fa-paperclip mr-2"></i>{{ count($taskDetail->taskFiles) }}</div>@endif
                                                        @if(count($taskDetail->comments) > 0)
                                                            <div class="action-item mr-2"><i class="fas fa-comment-alt mr-2"></i>{{ count($taskDetail->comments) }}</div>@endif
                                                        @if($taskDetail->checklist->count() > 0)
                                                            <div class="action-item mr-2"><i class="fas fa-tasks mr-2"></i>{{ $taskDetail->countTaskChecklist() }}</div>@endif
                                                    </div>
                                                </div>
                                                <div class="col-5">@if(!empty($taskDetail->end_date) && $taskDetail->end_date != '0000-00-00')<small @if(strtotime($taskDetail->end_date) < time())class="text-danger"@endif>{{ \App\Models\Utility::getDateFormated($taskDetail->end_date) }}</small>@endif</div>
                                                <div class="col-7 text-right">
                                                    @if($users = $taskDetail->users())
                                                        <div class="avatar-group">
                                                            @foreach($users as $key => $user)
                                                                @if($key<3)
                                                                    <a href="" class="avatar rounded-circle avatar-sm">
                                                                        <img {{ $user->img_avatar }} title="{{ $user->name }}">
                                                                    </a>
                                                                @else
                                                                    @break
                                                                @endif
                                                            @endforeach
                                                            @if(count($users) > 3)
                                                                <a href="#" class="avatar rounded-circle avatar-sm">
                                                                    <img avatar="+ {{ count($users)-3 }}">
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <span class="empty-container" data-placeholder="{{__('Empty')}}"></span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/libs/autosize/dist/autosize.min.js') }}"></script>
    <script src="{{ asset('assets/js/colorPick.js') }}"></script>
    <script>
        var now = "{{__('Now')}}";

        /*For Task Move To Update Stage*/
        !function (a) {
            "use strict";
            var t = function () {
                this.$body = a("body")
            };
            t.prototype.init = function () {
                a('[data-plugin="dragula"]').each(function () {
                    var t = a(this).data("containers"), n = [];
                    if (t) for (var i = 0; i < t.length; i++) n.push(a("#" + t[i])[0]); else n = [a(this)[0]];
                    var r = a(this).data("handleclass");
                    r ? dragula(n, {
                        moves: function (a, t, n) {
                            return n.classList.contains(r)
                        }
                    }) : dragula(n).on('drop', function (el, target, source, sibling) {
                        var sort = [];
                        $("#" + target.id + " > div").each(function () {
                            sort[$(this).index()] = $(this).attr('id');
                        });

                        var id = el.id;
                        var old_stage = $("#" + source.id).data('status');
                        var new_stage = $("#" + target.id).data('status');
                        var project_id = '{{$project->id}}';

                        $("#" + source.id).parent().find('.count').text($("#" + source.id + " > div").length);
                        $("#" + target.id).parent().find('.count').text($("#" + target.id + " > div").length);
                        $.ajax({
                            url: '{{route('tasks.update.order',[$project->id])}}',
                            type: 'PATCH',
                            data: {id: id, sort: sort, new_stage: new_stage, old_stage: old_stage, project_id: project_id},
                            success: function (data) {
                              
                                // console.log(data);
                            }
                        });
                    });
                })
            }, a.Dragula = new t, a.Dragula.Constructor = t
        }(window.jQuery), function (a) {
            "use strict";
            a.Dragula.init()
        }(window.jQuery);

        $(document).ready(function () {
            /*Set assign_to Value*/
            $(document).on('click', '.add_usr', function () {
                var ids = [];
                $(this).toggleClass('selected');
                var crr_id = $(this).attr('data-id');
                $('#usr_txt_' + crr_id).html($('#usr_txt_' + crr_id).html() == 'Add' ? '{{__('Added')}}' : '{{__('Add')}}');
                if ($('#usr_icon_' + crr_id).hasClass('fa-plus')) {
                    $('#usr_icon_' + crr_id).removeClass('fa-plus');
                    $('#usr_icon_' + crr_id).addClass('fa-check');
                } else {
                    $('#usr_icon_' + crr_id).removeClass('fa-check');
                    $('#usr_icon_' + crr_id).addClass('fa-plus');
                }
                $('.selected').each(function () {
                    ids.push($(this).attr('data-id'));
                });
                $('input[name="assign_to"]').val(ids);
            });

            $(document).on("click", ".del_task", function () {
                var id = $(this);
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'DELETE',
                    dataType: 'JSON',
                    success: function (data) {
                        $('#' + data.task_id).remove();
                        show_toastr('{{__('Success')}}', '{{ __("Task Deleted Successfully!")}}', 'success');
                    },
                });
            });

            /*For Task Comment*/
            $(document).on('click', '#comment_submit', function (e) {
                var curr = $(this);
                var comment = $.trim($("#form-comment textarea[name='comment']").val());
                if (comment != '') {
                    $.ajax({
                        url: $("#form-comment").data('action'),
                        data: {comment: comment},
                        type: 'POST',
                        success: function (data) {
                            data = JSON.parse(data);
                            var html = "<div class='list-group-item px-0'>" +
                                "                    <div class='row align-items-center'>" +
                                "                        <div class='col-auto'>" +
                                "                            <a href='#' class='avatar avatar-sm rounded-circle'>" +
                                "                                <img " + data.user.img_avatar + " alt='" + data.user.name + "'>" +
                                "                            </a>" +
                                "                        </div>" +
                                "                        <div class='col ml-n2'>" +
                                "                            <p class='d-block h6 text-sm font-weight-light mb-0 text-break'>" + data.comment + "</p>" +
                                "                            <small class='d-block'>" + now + "</small>" +
                                "                        </div>" +
                                "                        <div class='col-auto'><a href='#' class='delete-comment' data-url='" + data.deleteUrl + "'><i class='fas fa-trash-alt text-danger'></i></a></div>" +
                                "                    </div>" +
                                "                </div>";

                            $("#comments").prepend(html);
                            $("#form-comment textarea[name='comment']").val('');
                            load_task(curr.closest('.side-modal').attr('id'));
                            show_toastr('{{__('Success')}}', '{{ __("Comment Added Successfully!")}}', 'success');
                        },
                        error: function (data) {
                            show_toastr('{{__('Error')}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                        }
                    });
                } else {
                    show_toastr('{{__('Error')}}', '{{ __("Please write comment!")}}', 'error');
                }
            });
            $(document).on("click", ".delete-comment", function () {
                var btn = $(this);

                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'DELETE',
                    dataType: 'JSON',
                    success: function (data) {
                        load_task(btn.closest('.side-modal').attr('id'));
                        show_toastr('{{__('Success')}}', '{{ __("Comment Deleted Successfully!")}}', 'success');
                        btn.closest('.list-group-item').remove();
                    },
                    error: function (data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('{{__('Error')}}', data.message, 'error');
                        } else {
                            show_toastr('{{__('Error')}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                        }
                    }
                });
            });

            /*For Task Checklist*/
            $(document).on('click', '#checklist_submit', function () {
                var name = $("#form-checklist input[name=name]").val();
                if (name != '') {
                    $.ajax({
                        url: $("#form-checklist").data('action'),
                        data: {name: name},
                        type: 'POST',
                        success: function (data) {
                            data = JSON.parse(data);
                            load_task($('.side-modal').attr('id'));
                            show_toastr('{{__('Success')}}', '{{ __("Checklist Added Successfully!")}}', 'success');
                            var html = '<div class="card border shadow-none checklist-member">' +
                                '                    <div class="px-3 py-2 row align-items-center">' +
                                '                        <div class="col-10">' +
                                '                            <div class="custom-control custom-checkbox">' +
                                '                                <input type="checkbox" class="custom-control-input" id="check-item-' + data.id + '" value="' + data.id + '" data-url="' + data.updateUrl + '">' +
                                '                                <label class="custom-control-label h6 text-sm" for="check-item-' + data.id + '">' + data.name + '</label>' +
                                '                            </div>' +
                                '                        </div>' +
                                '                        <div class="col-auto card-meta d-inline-flex align-items-center ml-sm-auto">' +
                                '                            <a href="#" class="action-item delete-checklist" role="button" data-url="' + data.deleteUrl + '">' +
                                '                                <i class="fas fa-trash-alt text-danger"></i>' +
                                '                            </a>' +
                                '                        </div>' +
                                '                    </div>' +
                                '                </div>'

                            $("#checklist").append(html);
                            $("#form-checklist input[name=name]").val('');
                            $("#form-checklist").collapse('toggle');
                        },
                        error: function (data) {
                            data = data.responseJSON;
                            show_toastr('{{__('Error')}}', data.message, 'error');
                        }
                    });
                } else {
                    show_toastr('{{__('Error')}}', '{{ __("Please write checklist name!")}}', 'error');
                }
            });
            $(document).on("change", "#checklist input[type=checkbox]", function () {
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'POST',
                    dataType: 'JSON',
                    success: function (data) {
                        load_task($('.side-modal').attr('id'));
                        show_toastr('{{__('Success')}}', '{{ __("Checklist Updated Successfully!")}}', 'success');
                    },
                    error: function (data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('{{__('Error')}}', data.message, 'error');
                        } else {
                            show_toastr('{{__('Error')}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                        }
                    }
                });
            });
            $(document).on("click", ".delete-checklist", function () {
                var btn = $(this);
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'DELETE',
                    dataType: 'JSON',
                    success: function (data) {
                        load_task($('.side-modal').attr('id'));
                        show_toastr('{{__('Success')}}', '{{ __("Checklist Deleted Successfully!")}}', 'success');
                        btn.closest('.checklist-member').remove();
                    },
                    error: function (data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('{{__('Error')}}', data.message, 'error');
                        } else {
                            show_toastr('{{__('Error')}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                        }
                    }
                });
            });

            /*For Task Attachment*/
            $(document).on('click', '#file_submit', function () {
                var file_data = $("#task_attachment").prop("files")[0];
                if (file_data != '' && file_data != undefined) {
                    var formData = new FormData();
                    formData.append('file', file_data);

                    $.ajax({
                        url: $("#file_submit").data('action'),
                        type: 'POST',
                        data: formData,
                        cache: false,
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            $('#task_attachment').val('');
                            $('.attachment_text').html('{{__('Choose a file…')}}');
                            data = JSON.parse(data);
                            load_task(data.task_id);
                            show_toastr('{{__('Success')}}', '{{ __("File Added Successfully!")}}', 'success');

                            var delLink = '';
                            if (data.deleteUrl.length > 0) {
                                delLink = '<a href="#" class="action-item delete-comment-file" role="button" data-url="' + data.deleteUrl + '">' +
                                    '                                        <i class="fas fa-trash"></i>' +
                                    '                                    </a>';
                            }

                            var html = '<div class="card mb-3 border shadow-none task-file">' +
                                '                    <div class="px-3 py-3">' +
                                '                        <div class="row align-items-center">' +
                                '                            <div class="col-auto">' +
                                '                                <img src="{{asset('assets/img/icons/files')}}/' + data.extension + '.png" class="img-fluid" style="width: 40px;">' +
                                '                            </div>' +
                                '                            <div class="col ml-n2">' +
                                '                                <h6 class="text-sm mb-0">' +
                                '                                    <a href="#">' + data.name + '</a>' +
                                '                                </h6>' +
                                '                                <p class="card-text small text-muted">' + data.file_size + '</p>' +
                                '                            </div>' +
                                '                            <div class="col-auto actions">' +
                                '                                <a href="{{asset(Storage::url('tasks'))}}/' + data.file + '" download class="action-item" role="button">' +
                                '                                    <i class="fas fa-download"></i>' +
                                '                                </a>' +
                                delLink +
                                '                            </div>' +
                                '                        </div>' +
                                '                    </div>' +
                                '                </div>'

                            $("#comments-file").prepend(html);
                        },
                        error: function (data) {
                            data = data.responseJSON;
                            if (data.message) {
                                show_toastr('{{__('Error')}}', data.errors.file[0], 'error');
                                $('#file-error').text(data.errors.file[0]).show();
                            } else {
                                show_toastr('{{__('Error')}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                            }
                        }
                    });
                } else {
                    show_toastr('{{__('Error')}}', '{{ __("Please select file!")}}', 'error');
                }
            });
            $(document).on("click", ".delete-comment-file", function () {
                var btn = $(this);
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'DELETE',
                    dataType: 'JSON',
                    success: function (data) {
                        load_task(btn.closest('.side-modal').attr('id'));
                        show_toastr('{{__('Success')}}', '{{ __("File Deleted Successfully!")}}', 'success');
                        btn.closest('.task-file').remove();
                    },
                    error: function (data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('{{__('Error')}}', data.message, 'error');
                        } else {
                            show_toastr('{{__('Error')}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                        }
                    }
                });
            });

            /*For Favorite*/
            $(document).on('click', '#add_favourite', function () {
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'POST',
                    success: function (data) {
                        if (data.fav == 1) {
                            $('#add_favourite').addClass('action-favorite');
                        } else if (data.fav == 0) {
                            $('#add_favourite').removeClass('action-favorite');
                        }
                    }
                });
            });

            /*For Complete*/
            $(document).on('change', '#complete_task', function () {
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'POST',
                    success: function (data) {
                        if (data.com == 1) {
                            $("#complete_task").prop("checked", true);
                        } else if (data.com == 0) {
                            $("#complete_task").prop("checked", false);
                        }
                        $('#' + data.task).insertBefore($('#task-list-' + data.stage + ' .empty-container'));
                        load_task(data.task);
                    }
                });
            });

            /*Progress Move*/
            $(document).on('change', '#task_progress', function () {
                var progress = $(this).val();
                $('#t_percentage').html(progress);
                $.ajax({
                    url: $(this).attr('data-url'),
                    data: {progress: progress},
                    type: 'POST',
                    success: function (data) {
                        load_task(data.task_id);
                    }
                });
            });
        });

        function load_task(id) {
            $.ajax({
                url: "{{route('projects.tasks.get','_task_id')}}".replace('_task_id', id),
                dataType: 'html',
                success: function (data) {
                    $('#' + id).html('');
                    $('#' + id).html(data);
                }
            });
        }
    </script>
@endpush
