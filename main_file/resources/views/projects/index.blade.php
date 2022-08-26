@extends('layouts.admin')

@section('title')
    {{__('Projects')}}
@endsection

@push('theme-script')
    <script src="{{ asset('assets/libs/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
@endpush

@section('action-button')
<div class="d-flex align-items-center  justify-content-between">
    @if($view == 'grid')
        <a href="{{ route('projects.list','list') }}" class="btn btn-sm bg-white btn-icon rounded-pill ml-2">
            <span class="btn-inner--text text-dark">{{__('List View')}}</span>
        </a>
    @else
        <a href="{{ route('projects.index') }}" class="btn btn-sm bg-white btn-icon rounded-pill mr-2 m-0">
            <span class="btn-inner--text text-dark">{{__('Card View')}}</span>
        </a>
    @endif 
 
    
    <div class="bg-neutral rounded-pill d-inline-block ml-2">
        <div class="input-group input-group-sm input-group-merge input-group-flush">
            <div class="input-group-prepend">
                <span class="input-group-text bg-transparent"><i class="fas fa-search"></i></span>
            </div>
            <input type="text" id="project_keyword" class="form-control form-control-flush" placeholder="{{__('Search by Name or tag')}}">
        </div>
    </div>
</div>
<div class="d-flex align-items-center justify-content-end mt-2 mt-md-0">
    <div class="dropdown btn btn-sm btn-white btn-icon-only rounded-circle ml-2 m-0">
        <a href="#" class="action-item text-dark line_height_auto" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-filter"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right dropdown-steady" id="project_sort">
            <a class="dropdown-item active" href="#" data-val="created_at-desc">
                <i class="fas fa-sort-amount-down"></i>{{__('Newest')}}
            </a>
            <a class="dropdown-item" href="#" data-val="created_at-asc">
                <i class="fas fa-sort-amount-up"></i>{{__('Oldest')}}
            </a>
            <a class="dropdown-item" href="#" data-val="name-asc">
                <i class="fas fa-sort-alpha-down"></i>{{__('From A-Z')}}
            </a>
            <a class="dropdown-item" href="#" data-val="name-desc">
                <i class="fas fa-sort-alpha-up"></i>{{__('From Z-A')}}
            </a>
        </div>
    </div>
    <div class="dropdown btn btn-sm btn-white btn-icon-only rounded-circle ml-2 m-0">
        <a href="#" class="action-item text-dark line_height_auto" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-flag"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right project-filter-actions dropdown-steady" id="project_status">
            <a class="dropdown-item filter-action filter-show-all pl-4 active" href="#">{{__('Show All')}}</a>
            @foreach(\App\Models\Project::$status as $key => $val)
                <a class="dropdown-item filter-action pl-4" href="#" data-val="{{ $key }}">{{__($val)}}</a>
            @endforeach
        </div>
    </div> 
  
    @if($allow == true)
        <a href="#" class="btn btn-sm btn-white btn-icon-only rounded-circle ml-2" data-url="{{ route('projects.create') }}" data-ajax-popup="true" data-size="lg" data-title="{{__('Create Project')}}">
            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
        </a>
    @else
        <a href="#" class="btn btn-sm btn-white btn-icon-only rounded-circle ml-2" id="prevent_project">
            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
        </a>
    @endif

    <a href="{{route('project.export')}}" class="btn btn-sm btn-white btn-icon-only rounded-circle ml-2" data-title="{{__('Export Project CSV file')}}" data-toggle="tooltip">
        <i class="fa fa-file-excel"></i>
    </a>

    <a href="#" class="btn btn-sm btn-white btn-icon-only rounded-circle" data-url="{{ route('project.file.import') }}" data-ajax-popup="true" data-title="{{__('Import Project CSV file')}}" data-toggle="tooltip">
        <i class="fa fa-file-csv"></i> 
    </a>
</div>
@endsection

@section('content')
    <div class="row min-750" id="project_view"></div>
@endsection

@push('script')
    <script>
        // ready
        $(function () {
            var sort = 'created_at-desc';
            var status = '';
            ajaxFilterProjectView('created_at-desc');

            // when change status
            $(".project-filter-actions").on('click', '.filter-action', function (e) {
                if ($(this).hasClass('filter-show-all')) {
                    $('.filter-action').removeClass('active');
                    $(this).addClass('active');
                } else {
                    $('.filter-show-all').removeClass('active');
                    if ($(this).hasClass('active')) {
                        $(this).removeClass('active');
                        $(this).blur();
                    } else {
                        $(this).addClass('active');
                    }
                }

                var filterArray = [];
                var url = $(this).parents('.project-filter-actions').attr('data-url');
                $('div.project-filter-actions').find('.active').each(function () {
                    filterArray.push($(this).attr('data-val'));
                });

                status = filterArray;

                ajaxFilterProjectView(sort, $('#project_keyword').val(), status);
            });

            // when change sorting order
            $('#project_sort').on('click', 'a', function () {
                sort = $(this).attr('data-val');
                ajaxFilterProjectView(sort, $('#project_keyword').val(), status);
                $('#project_sort a').removeClass('active');
                $(this).addClass('active');
            }); 

        

            // when searching by project name
            $(document).on('keyup', '#project_keyword', function () {
                ajaxFilterProjectView(sort, $(this).val(), status);
            });

            // project invite modal
            var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

            $(document).on('click', '.check-invite-members', function (e) {
                var ele = $(this);
                var emailele = $('#invite_email');
                var project_id = $('input[name="project_id"]').val();
                var email = emailele.val();
                var role = $('#usr_role').val();

                // Email Field Validation
                $('.email-error-message').remove();
                if (email == '') {
                    emailele.focus().after('<small class="email-error-message text-danger">{{ __("This field is required.") }}</small>');
                    return false;
                }

                if (!emailReg.test(email)) {
                    emailele.focus().after('<small class="email-error-message text-danger">{{ __("Please enter valid email address.") }}</small>');
                    return false;
                } else {
                    $('.invite_usr').addClass('d-none');

                    $.ajax({
                        url: '{{ route('user.exists') }}',
                        dataType: 'json',
                        data: {
                            'project_id': project_id,
                            'email': email,
                            'role': role
                        },
                        success: function (data) {
                            if (data.code == '202') {
                                $('#commonModal').modal('hide');
                                show_toastr(data.status, data.success, 'success');
                            } else if (data.code == '200') {
                                $('#commonModal').modal('hide');
                                show_toastr(data.status, data.success, 'success');
                                location.reload();
                            } else if (data.code == '404') {
                                $('.invite_user_div').removeClass('d-none');
                                $('.invite-warning').text(data.error).show();
                                $('#invite_email').prop('readonly', true);
                            }
                            ele.removeClass('check-invite-members').addClass('invite-members');
                        }
                    });
                }
            });

            $(document).on('click', '.invite-members', function () {
                var project_id = $('input[name="project_id"]').val();
                var useremail = $('#invite_email').val();
                var username = $('#username').val();
                var userpassword = $('#userpassword').val();
                var role = $('#usr_role').val();

                $('.username-error-message').remove();
                if (username == '') {
                    $('#username').focus().after('<small class="username-error-message text-danger">{{ __("This field is required.") }}</small>');
                    return false;
                }

                $('.userpassword-error-message').remove();
                if (userpassword == '') {
                    $('#userpassword').focus().after('<small class="userpassword-error-message text-danger">{{ __("This field is required.") }}</small>');
                    return false;
                }

                $('.email-error-message').remove();
                if (useremail == '') {
                    $('#invite_email').focus().after('<small class="email-error-message text-danger">{{ __("This field is required.") }}</small>');
                    return false;
                }

                if (!emailReg.test(useremail)) {
                    $('#invite_email').focus().after('<small class="email-error-message text-danger">{{ __("Please enter valid email address.") }}</small>');
                    return false;
                } else {
                    $.ajax({
                        url: '{{ route('invite.project.user.member') }}',
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            'project_id': project_id,
                            'useremail': useremail,
                            'username': username,
                            'userpassword': userpassword,
                            'role': role,
                        },
                        success: function (data) {
                            if (data.code == '200') {
                                $('#commonModal').modal('hide');
                                show_toastr(data.status, data.success, 'success')
                                if ($('#project_users').length > 0) {
                                    loadProjectUser();
                                } else {
                                    ajaxFilterProjectView('created_at-desc', $('#project_keyword').val());
                                }
                            } else if (data.code == '404') {
                                show_toastr(data.status, data.error, 'error')
                            }
                        }
                    });
                }
            });

            $(document).on('click', '.invite-btn', function () {
                var current = $(this);
                var id = current.attr('data-id');
                var project_id = $('input[name="project_id"]').val();
                var role = $('#usr_role').val();

                $.ajax({
                    url: '{{ route('user.exists') }}',
                    dataType: 'json',
                    data: {
                        'project_id': project_id,
                        'id': id,
                        'role': role
                    },
                    success: function (data) {
                        if (data.code == '200') {
                            current.html('Invited');
                            current.removeClass('btn-secondary');
                            current.addClass('btn-primary');

                            show_toastr(data.status, data.success, 'success');

                            if ($('#project_users').length > 0) {
                                loadProjectUser();
                            } else {
                                ajaxFilterProjectView('created_at-desc', $('#project_keyword').val());
                            }
                        } else if (data.code == '202') {
                            show_toastr(data.status, data.success, 'success');
                        } else if (data.code == '404') {
                            show_toastr(data.status, data.error, 'error');
                        }
                    }
                });
            });

            $(document).on('click', '#prevent_project', function () {
                show_toastr('Error', '{{__('Your project limit is over, Please upgrade plan.')}}', 'error');
            });

            $(document).on('click', '.user_role', function () {
                $('#usr_role').val($(this).attr('data-val'));
            })
        }); 

        
        // For Filter
        var currentRequest = null;

        function ajaxFilterProjectView(project_sort, keyword = '', status = '') {
            var mainEle = $('#project_view');
            var view = '{{$view}}';
            var data = {
                view: view,
                sort: project_sort,
                keyword: keyword,
                status: status,
            }

            currentRequest = $.ajax({
                url: '{{ route('filter.project.view') }}',
                data: data,
                beforeSend: function () {
                    if (currentRequest != null) {
                        currentRequest.abort();
                    }
                },
                success: function (data) {
                    mainEle.html(data.html);
                    $('[id^=fire-modal]').remove();
                    loadConfirm();
                }
            });
        } 

           
    </script> 

    <script>
                //  Upload image
                $(document).on('click', '.image, .edit-product', function() {
                    
                    setTimeout(function() {
                        img_display();
                    }, 1000);
                });

            // $(document).on('click', '.product-img-btn', function() {
            // $('input[name="imgstatus"]').val(1);
            // $(this).closest('#product-image').find('img').attr("src", "");
            // $(this).closest('#product-image').find('button').addClass('d-none');
            // }); 

            function img_display() {
                
            if ($('#product-image img.profile-image').attr('src') == undefined) {
                
            $("#product-image img.profile-image").addClass('d-none');
            } else {
            $("#product-image img.profile-image").removeClass('d-none');
            }

            } 



    </script>
@endpush
