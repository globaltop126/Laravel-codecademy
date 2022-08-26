@extends('layouts.admin')
@section('title')
    {{__('Project Report')}}
@endsection 
@section('action-button')
<div class="d-flex align-items-center justify-content-end mt-2 mt-md-0 filter">
    <div class="dropdown btn btn-sm btn-white btn-icon-only rounded-circle ml-2 m-0">
        <a href="#" class="action-item text-dark line_height_auto" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-filter"></i>
        </a>
       
    </div>
</div>
@endsection   



@section('content')
     <div class="row  display-none d-flex align-items-center justify-content-center" id="show_filter">

          <div class="bg-neutral rounded-pill d-inline-block ml-2">
        <div class="input-group input-group-sm input-group-merge input-group-flush">
            <div class="input-group-prepend">
                <span class="input-group-text bg-transparent"><i class="fas fa-search"></i></span>
            </div>
            <input type="text" id="project_keyword" name="name" class="form-control form-control-flush" placeholder="{{__('Search by Name or tag')}}">
        </div>
    </div>
            <div class="col-auto">
                <select class="form-control form-control-sm w-auto " name="all_users" id="all_users">
                    <option value="" class="">{{ __('All Users') }}</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        <div class="col-auto">
            <select class=" form-control form-control-sm w-auto " name="status" id="status">
                <option value="" class="">{{ __('All Status') }}</option>
                    @foreach ($project_status as $statuses)
                        <option value="{{ $statuses->status }}">{{ $statuses->status }}</option>
                    @endforeach
        
            </select>
        </div>
         

        <div class=" col-auto">  
           
            <input class="form-control form-control-sm w-auto" type="date" id="start_date" name="start_date" value="" autocomplete="off" required="required"  placeholder="{{ __('Start Date') }}">
      </div>


        <div class=" col-auto">  
           
            <input class="form-control form-control-sm w-auto" type="date" id="end_date" name="end_date" value="" autocomplete="off" required="required"  placeholder="{{ __('End Date') }}">
      </div>


      <button class="btn btn-white btn-sm ml-2  col-auto btn-filter apply" id="filter"><i class="mdi mdi-check"></i>{{ __('Apply') }}</button>

      
    </div> 
    <div class="row">
        <div class="">
                <div class="card mt-4">
                              <div class="table-responsive">
                                <table class="table align-items-center  mt-2" id="selection-datatable1">
                                    <thead>
                                        <tr>

                                            <th scope="col" class="sort"> {{__('#')}}</th>
                                            <th scope="col" class="sort"> {{__('Project Name')}}</th>
                                            <th scope="col" class="sort"> {{__('Start Date')}}</th>
                                            <th scope="col" class="sort"> {{__('Due Date')}}</th>
                                            <th scope="col" class="sort"> {{__('Project Member')}}</th>
                                            <th scope="col" class="sort"> {{__('Progress')}}</th>
                                            <th scope="col" class="sort">{{__('Project Status')}}</th>
                                            <th scope="col" class="sort">{{__('Action')}}</th>
                                        </tr>
                                    </thead>
                                     <tbody class="list">
                                     
                                    </tbody>
                                </table>
                            </div>
                          </div>
                    </div>
               
            </div>
       </div>



@endsection
@push('css')
  <style>  
 .display-none {
            display: none !important;
        }

    </style>
@endpush

@push('script')




    <script type="text/javascript">
        $(".filter").click(function() {
            $("#show_filter").toggleClass('display-none');
        });

          $(document).on('keyup', '#project_keyword', function () {
                ajaxFilterProjectView(sort, $(this).val(), status);
            });
    </script>


     <script>
        const table = $("#selection-datatable1");
        $(document).ready(function() {


                  
                
            $(document).on("click", ".btn-filter", function() {
               
                getData();
            });

            function getData() {

                // table.clear().draw();
                 $("#selection-datatable1 tbody tr").html(
                    '<td colspan="11" class="text-center"> {{ __('Loading ...') }}</td>');

               var data = {
                    status: $("#status").val(),
                    start_date: $("#start_date").val(),
                    end_date  : $("#end_date").val(),
                    all_users :$("#all_users").val(),
                     name :$("#project_keyword").val(),
                };
                $.ajax({
                    url: '{{ route('projects.ajax') }}',
                    type: 'POST',
                    data: data,
                    success: function(data) {  
                         
                        // $('#formdata')[0].reset();
                         $("tbody").html("");
                         $.each(data.data, function( index, value ) {

                             console.log(value.members);
                            var row =  $("<tr><td>" 
                            + value.id + "</td><td>" 
                            + value.name + "</td><td>" 
                            + value.start_date + "</td><td>" 
                            + value.end_date + "</td><td>" 
                             + value.members + "</td><td>" 
                             + value.Progress + "</td><td>" 
                             + value.status + "</td><td>" 
                            + value.action + "</td></tr>");

                            $("tbody").append(row);
                        });
                    
                        // loadConfirm();
                    },
                    error: function(data) {
                        show_toastr('Info', data.error, 'error')
                    }
                })
            }

            getData();

        });
    </script>

@endpush
