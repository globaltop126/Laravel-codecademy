@extends('layouts.admin')

@section('title')
    {{__('Task Calendar')}}
@endsection

@push('theme-script')
    <script src="{{ asset('assets/libs/dragula/dist/dragula.min.js') }}"></script>
@endpush

@section('content')
    <div class="page-title">
        <div class="row pb-3">
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <h5 class="h4 d-inline-block font-weight-400 mb-0 text-white">{{__('Calendar')}}</h5>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 text-right">
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-secondary btn-sm {{($task_by == 'my') ? 'active' : ''}}">
                        <input type="checkbox" name="options" id="my_task" autocomplete="off" {{($task_by == 'my') ? 'checked' : ''}}>{{__('See My Task')}}
                    </label>
                </div>
                <select class="form-control form-control-sm w-auto d-inline" size="sm" name="project" id="project">
                    <option value="">{{__('All Projects')}}</option>
                    @foreach(Auth::user()->projects as $project)
                        <option value="{{$project->id}}" {{ ($project_id == $project->id) ? 'selected' : ''}}>{{ $project->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-white btn-sm ml-2" id="filter"><i class="mdi mdi-check"></i>{{__('Apply')}}</button>
            </div>
        </div>
        <div class="row justify-content-between align-items-center">
            <div class="col d-flex align-items-center">
                <h5 class="fullcalendar-title h4 d-inline-block font-weight-400 mb-0 text-white">{{__('Calendar')}}</h5>
            </div>
            <div class="col-lg-6 mt-3 mt-lg-0 text-lg-right">
                <div class="btn-group" role="group" aria-label="Basic example">
                    <a href="#" class="fullcalendar-btn-prev btn btn-sm btn-neutral">
                        <i class="fas fa-angle-left"></i>
                    </a>
                    <a href="#" class="fullcalendar-btn-next btn btn-sm btn-neutral">
                        <i class="fas fa-angle-right"></i>
                    </a>
                </div>
                <div class="btn-group" role="group" aria-label="Basic example">
                    <a href="#" class="btn btn-sm btn-neutral" data-calendar-view="month">{{__('Month')}}</a>
                    <a href="#" class="btn btn-sm btn-neutral" data-calendar-view="basicWeek">{{__('Week')}}</a>
                    <a href="#" class="btn btn-sm btn-neutral" data-calendar-view="basicDay">{{__('Day')}}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card overflow-hidden">
                <div class="calendar" data-toggle="task-calendar"></div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        var e, t, a = $('[data-toggle="task-calendar"]');
        a.length && (t = {
            header: {right: "", center: "", left: ""},
            buttonIcons: {prev: "calendar--prev", next: "calendar--next"},
            theme: !1,
            selectable: !0,
            selectHelper: !0,
            editable: !0,
            events: {!! json_encode($arrTasks) !!} ,
            eventStartEditable: !1,
            locale: '{{basename(App::getLocale())}}',
            dayClick: function (e) {
                var t = moment(e).toISOString();
                $("#new-event").modal("show"), $(".new-event--title").val(""), $(".new-event--start").val(t), $(".new-event--end").val(t)
            },
            eventResize: function (event) {
                var eventObj = {
                    start: event.start.format(),
                    end: event.end.format(),
                };

                $.ajax({
                    url: event.resize_url,
                    method: 'POST',
                    data: eventObj,
                    success: function (response) {
                    },
                    error: function (data) {
                        data = data.responseJSON;
                    }
                });
            },
            viewRender: function (t) {
                e.fullCalendar("getDate").month(), $(".fullcalendar-title").html(t.title)
            },
            eventClick: function (e, t) {
                var title = e.title;
                var url = e.url;

                if (typeof url != 'undefined') {
                    $("#commonModal .modal-title").html(title);
                    $("#commonModal .modal-dialog").addClass('modal-md');
                    $("#commonModal").modal('show');
                    $.get(url, {}, function (data) {
                        $('#commonModal .modal-body').html(data);
                    });
                    return false;
                }
            }
        }, (e = a).fullCalendar(t),
            $("body").on("click", "[data-calendar-view]", function (t) {
                t.preventDefault(), $("[data-calendar-view]").removeClass("active"), $(this).addClass("active");
                var a = $(this).attr("data-calendar-view");
                e.fullCalendar("changeView", a)
            }), $("body").on("click", ".fullcalendar-btn-next", function (t) {
            t.preventDefault(), e.fullCalendar("next")
        }), $("body").on("click", ".fullcalendar-btn-prev", function (t) {
            t.preventDefault(), e.fullCalendar("prev")
        }));

        $(document).on("click", "#filter", function () {
            if ($('#my_task').is(":checked")) {
                window.location.href = "{{route('task.calendar',['my'])}}/" + $("#project").val();
            } else {
                window.location.href = "{{route('task.calendar',['all'])}}/" + $("#project").val();
            }
        });
    </script>
@endpush
