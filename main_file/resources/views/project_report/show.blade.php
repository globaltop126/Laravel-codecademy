@extends('layouts.admin')

@section('title') {{__('Project Detail')}} @endsection

@section('action-button')
<div class="d-flex align-items-center  justify-content-between">
    <div class="dropdown btn btn-sm btn-white btn-icon-only rounded-circle ml-2 m-0">
        <a href="#" onclick="saveAsPDF()" class="action-item text-dark line_height_auto" data-toggle="tooltip" title="{{ __('Download') }}">
            <i class="fas fa-print mt-2"></i>
        </a>
    </div>
    </div>

 @endsection 


@section('content')
  <div class="row">
            <!-- [ sample-page ] start --> 
        <div class="col-sm-12">
             <div class="row">
                <div  class= "row" id="printableArea">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header">
                            <h6>{{ __('Overview')}}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-7">
                               
                                      <table class="table" id="pc-dt-simple">
                                        <tbody>
                                         <tr class="table_border" >
                                            <th class="table_border" >{{ __('Project Name')}}:</th>
                                            <td class="table_border">{{$project->name}}</td>
                                        </tr>
                                        <tr>
                                            <th class="table_border">{{ __('Project Status')}}:</th>
                                            <td class="table_border">

                                                       <span class="badge badge-pill badge-{{\App\Models\Project::$status_color[$project->status]}}">{{ __(\App\Models\Project::$status[$project->status]) }}</span>
                                            </td>
                                        </tr>
                                        <tr role="row">
                                            <th class="table_border">{{ __('Start Date') }}:</th>
                                            <td class="table_border">{{$project->start_date}}</td>
                                        </tr>
                                        <tr>
                                            <th class="table_border">{{ __('Due Date') }}:</th>
                                            <td class="table_border">{{$project->end_date}}</td>
                                        </tr>
                                        <tr>
                                            <th class="table_border">{{ __('Total Members')}}:</th>
                                            <td class="table_border">{{count($users)}}</td>
                                        </tr>
                                    </tbody>
                                   </table>
                                      </div>
                                  <div class="col-5 ">
                                   <!--  <div id="projects-chart"></div> -->

                                        @php
                                         $task_percentage = $project->project_progress()['percentage'];
                                         $data =trim($task_percentage,'%');
                                            $status = $data > 0 && $data <= 25 ? 'red' : ($data > 25 && $data <= 50 ? 'orange' : ($data > 50 && $data <= 75 ? 'blue' : ($data > 75 && $data <= 100 ? 'green' : '')));
                                        @endphp

                                     <div class="circular-progressbar p-0">
                                                            <div class="flex-wrapper">
                                                                <div class="single-chart">
                                                                    <svg viewBox="0 0 36 36"
                                                                        class="circular-chart orange {{ $status }}">
                                                                        <path class="circle-bg" d="M18 2.0845
                                                                                  a 15.9155 15.9155 0 0 1 0 31.831
                                                                                  a 15.9155 15.9155 0 0 1 0 -31.831" />
                                                                        <path class="circle"
                                                                            stroke-dasharray="{{ $data }}, 100" d="M18 2.0845
                                                                                  a 15.9155 15.9155 0 0 1 0 31.831
                                                                                  a 15.9155 15.9155 0 0 1 0 -31.831" />
                                                                        <text x="18" y="20.35"
                                                                            class="percentage">{{ $data }}%</text>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                            </div>
                                                         </div>
                                                    </div>
                                              </div>
                                            </div>
                                        </div>
                                       

                                        <div class="col-md-5">
                                            <div class="card">
                                               <div class="card-header">
                                                 <h6>{{ __('Milestone Progress')}}</h6>
                                             </div>

                                              @php
                                          $mile_percentage = $project->project_milestone_progress()['percentage'];
                                          $mile_percentage =trim($mile_percentage,'%');
                                          @endphp

                                            <div class="card-body">
                                                
                                           <!-- <div class="d-flex justify-content-center"> -->
                                            <div id="milestone-chart" class="d-flex justify-content-center"></div>
                                       <!--  </div> -->
                                              </div>
                                        </div>
                                     </div>
                                      <div class="col-md-3">
                                          <div class="card">
                                            <div class="card-header">
                                                <div class="float-end">
                                                    <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="Refferals"><i
                                                            class=""></i></a>
                                                </div>
                                                <h6>{{ __('Task Priority') }}</h6>
                                            </div>
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-12">
                                                           <!--  <div id="projects-chart"></div> -->
                                                           <div id='chart_priority'></div>
                                                        </div>
                                                
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-md-5">
                                          <div class="card">
                                            <div class="card-header">
                                                <div class="float-end">
                                                    <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="Refferals"><i
                                                            class=""></i></a>
                                                </div>
                                                <h6>{{ __('Task Status') }}</h6>
                                            </div>
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-12">
                                                            <div id="chart"></div>
                                                        </div>
                                                   
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-md-4">
                                          <div class="card">
                                            <div class="card-header">
                                                <div class="float-end">
                                                    <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="Refferals"><i
                                                            class=""></i></a>
                                                </div>
                                                <h6>{{ __('Hours Estimation') }}</h6>
                                            </div>
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-12">
                                                            <div id="chart-hours"></div>
                                                        </div>
                                                   
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                    
                            <div class="col-md-6">
                                <div class="card">
                                       <div class="card-header">
                                                <h6>{{ __('Users') }}</h6>
                                            </div>
                                    <div class="card-body table-border-style ">
                                        <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{__('Name')}}</th>
                                            <th>{{__('Assigned Tasks')}}</th>
                                            <th>{{__('Done Tasks')}}</th>
                                            <th>{{__('Logged Hour')}}</th>
                                           

                                        </tr>
                                    </thead>
                                     <tbody>

                                        @foreach($project->users as $user)

                                        @php
                                        $hours_format_number = 0;
                                        $total_hours = 0;
                                        $hourdiff_late = 0;
                                        $esti_late_hour =0;
                                        $esti_late_hour_chart=0;


                                         $total_user_task = App\Models\ProjectTask::where('project_id',$project->id)->whereRaw("FIND_IN_SET(?,  assign_to) > 0", [$user->id])->get()->count();

                                          $all_task = App\Models\ProjectTask::where('project_id',$project->id)->whereRaw("FIND_IN_SET(?,  assign_to) > 0", [$user->id])->get();

                                          $total_complete_task =  
                                          App\Models\ProjectTask::join('task_stages','task_stages.id','=','project_tasks.stage_id')->where('project_tasks.project_id','=',$project->id)->where('assign_to','=',$user->id)->where('task_stages.complete','=','1')->get()->count();


                                           $logged_hours = 0;
                                          $timesheets = App\Models\Timesheet::where('project_id',$project->id)->where('created_by' ,$user->id)->get(); 
                                          @endphp
                                               



                                          @foreach($timesheets as $timesheet)
                                           @php
                                          $date_time = $timesheet->time;
                                          $hours =  date('H', strtotime($date_time));
                                          $minutes =  date('i', strtotime($date_time));
                                          $total_hours = $hours + ($minutes/60) ;
                                          $logged_hours += $total_hours ;
                                          $hours_format_number = number_format($logged_hours, 2, '.', '');
                                           @endphp
                                           @endforeach
                                       
                                        <tr>
                                         <td>{{$user->name}}</td>
                                         <td>{{$total_user_task}}</td>
                                         <td>{{$total_complete_task}}</td>
                                         <td>{{$hours_format_number}}</td>
                                        </tr>
                                        @endforeach
                                       
                                    </tbody>
                                </table>
                            </div>
                        </div>
                      </div>
                  </div>
            
                          <div class="col-md-6">
                                <div class="card">
                                       <div class="card-header">
                                                <h6>{{ __('Milestones') }}</h6>
                                            </div>
                                    <div class=" table-border-style">
                                      <div class="card-body">
                                <div class="table-responsive">
                                    <table class=" table " >
                                    <thead>
                                        <tr>
                                            <th> {{__('Name')}}</th>
                                            <th> {{__('Progress')}}</th>
                                            <th> {{__('Status')}}</th>
                                            <th> {{__('Start Date')}}</th>
                                            <th> {{__('End Date')}}</th>
                                        </tr>
                                    </thead>
                                     <tbody>
                                       @foreach($project->milestones as $key => $milestone)
                                        <tr>
                                           <td>{{$milestone->title}}</td>
                                           <td>
                                           <div class="progress_wrapper">
                                                       <div class="progress">
                                                          <div class="progress-bar" role="progressbar"  style="width: {{ $milestone->progress }}px;"
                                                             aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                                                       </div>
                                                       <div class="progress_labels">
                                                          <div class="total_progress">
                                                          
                                                             <strong> {{ $milestone->progress }}%</strong>
                                                          </div>
                                                     
                                                       </div>
                                                    </div>
                                                    </td>
                                           
                                           <td>  <span class="badge badge-pill badge-{{\App\Models\Project::$status_color[$milestone->status]}}">{{ __(\App\Models\Project::$status[$milestone->status]) }}</span></td>
                                           <td>{{$milestone->start_date}}</td>
                                           <td>{{$milestone->end_date}}</td>
                                           
                                      
                                        </tr>
                                         @endforeach
                                    
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>

    <div class="mt-3 mb-3 row d-flex align-items-center justify-content-between col-12" id="show_filter">
            <div class="col-auto ">
                <select class="form-control form-control-sm w-auto" name="all_users" id="all_users">
                    <option value="" class="px-4">{{ __('All Users') }}</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        <div class="col-auto">
                <select class="form-control form-control-sm w-auto" name="milestone_id" id="milestone_id">
                    <option value="" class="px-4">{{ __('All Milestones') }}</option>
                    @foreach ($milestones as $milestone)
                        <option value="{{ $milestone->id }}">{{ $milestone->title }}</option>
                    @endforeach
                </select>
            </div>
        <div class="col-auto">
            <select class="form-control form-control-sm w-auto" name="status" id="status">
                <option value="" class="px-4">{{ __('All Status') }}</option>
                @foreach ($stages as $stage)
                    <option value="{{ $stage->id }}">{{ __($stage->name) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <select class="form-control form-control-sm w-auto"  name="priority" id="priority">
                <option value="" class="px-4">{{ __('All Priority') }}</option>
                <option value="low">{{ __('Low') }}</option>
                <option value="medium">{{ __('Medium') }}</option>
                <option value="critical">{{ __('Critical') }}</option>
                <option value="high">{{ __('High') }}</option>
            </select>
        </div>
      
       <button class="btn btn-white btn-sm  col-auto btn-filter apply" id="filter"><i class="mdi mdi-check"></i>{{ __('Apply') }}</button>

         <button class=" btn btn-white btn-sm  col-auto ">  <a href="{{ route('project_report.export' ,$project->id)}}" class="btn-white">
              <i class="mdi mdi-check"></i>
                {{ __('Export') }}
                </a></button>
     </div>


       <div class="col-md-12">
    <div class="card">

        <div class="card-header">
            <h6>{{ __('Tasks') }}</h6>
        </div>
        <div class="card-body mx-2">
            <div class="row">
         
                    <div class="table-responsive">
                        <table class="table"
                            id="tasks-selection-datatable">
                            <thead>
                                <th>{{ __('Task Name') }}</th>
                                <th>{{ __('Milestone') }}</th>
                                 <th>{{ __('Start Date') }}</th>
                                <th>{{ __('Due Date') }}</th>
                             
                                    <th>{{ __('Assigned to') }}</th>
                                
                                <th> {{__('Total Logged Hours')}}</th>
                                <th>{{ __('Priority') }}</th>
                                <th>{{ __('Status') }}</th>
                                
                            </thead>
                            <tbody class="task">

                            </tbody>
                        </table>
                    </div>
                
            </div>
        </div>
      </div>
  </div>

           
                     
                                 </div>
                <!-- [ sample-page ] end -->
            </div>
            <!-- [ Main Content ] end -->
        </div>
        @endsection


@push('css-page')

@endpush
<style type="text/css">
    .apexcharts-menu-icon {
        display: none;
    }
      table.dataTable.no-footer {
    border-bottom: none !important;
} 
    .table_border{
    border: none !important
    }
</style>


@push('script')

<script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
<script>
          var filename = $('#chart-hours').val();

        function saveAsPDF() {

            var element = document.getElementById('printableArea');

           
            var opt = {
                margin: 0.3,
              
                image: {
                    type: 'jpeg',
                    quality: 1
                },
                html2canvas: {
                    scale: 4,
                    dpi: 72,
                    letterRendering: true
                },
                jsPDF: {
                    unit: 'in',
                    format: 'A2'
                }
            };
            html2pdf().set(opt).from(element).save();
        }

    </script>

<script src="{{asset('assets/js/apexcharts.min.js')}}"></script>
<script>
        $(document).ready(function() {
           
             
            $(document).on("click", ".btn-filter", function() {

                getData();
            });

            function getData() {
               // table.clear().draw();
                 $("#tasks-selection-datatable tbody tr").html(
                    '<td colspan="11" class="text-center"> {{ __('Loading ...') }}</td>');

               var data = {
                    
                    assign_to: $("#all_users").val(),
                    priority: $("#priority").val(),
                    due_date_order: $("#due_date_order").val(),
                    milestone_id:  $("#milestone_id").val(),
                    start_date: $("#start_date").val(),
                    due_date:  $("#due_date").val(),
                    status: $("#status").val(),
                };
                $.ajax({
                    url: '{{ route('tasks.report.ajaxdata',$project->id) }}',
                    type: 'POST',
                    data: data,
                    success: function(data){  
                         
                        // $('#formdata')[0].reset();
                         $(".task").html("");
                         $.each(data.data, function( index, value ) {

                             
                            var row =  $("<tr><td>" 
                            + value.name + "</td><td>" 
                             + value.milestone_id + "</td><td>" 
                            + value.start_date + "</td><td>" 
                            + value.end_date + "</td><td>" 
                             + value.user_name + "</td><td>" 
                              + value.logged_hours + "</td><td>" 
                             + value.priority + "</td><td>" 
                             + value.status + "</td></tr>");

                            $(".task").append(row);
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

   <script>
           (function () {
        var options = {
            series: [{!! json_encode($mile_percentage) !!}],
            chart: {
                height: 475,
                type: 'radialBar',
                offsetY: -20,
                sparkline: {
                    enabled: true
                }
            },
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 90,
                    track: {
                        background: "#e7e7e7",
                        strokeWidth: '97%',
                        margin: 5, // margin is in pixels
                    },
                    dataLabels: {
                        name: {
                            show: true
                        },
                        value: {
                            offsetY: -50,
                            fontSize: '20px'
                        }
                    }
                }
            },
            grid: {
                padding: {
                    top: -10
                }
            },
            colors: ["#51459d"],
            labels: ['Progress'],
        };
        var chart = new ApexCharts(document.querySelector("#milestone-chart"), options);
        chart.render();
    })();


var options = {
          series:  {!! json_encode($arrProcessPer_status_task) !!},
          chart: {
          width: 380,
          type: 'pie',
        },
         colors: ["#23d180","#719fea","#edf104","#e92f2f"],
        labels:{!! json_encode($arrProcess_Label_status_tasks) !!},
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 100
            },
            legend: {
              position: 'bottom'

            }
          }
        }]
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();



     var options = {
          series: [{
          data: {!! json_encode($arrProcessPer_priority) !!}
        }],
          chart: {
          height: 210,
          width: 200,
          type: 'bar',
        },
        colors: ['#6fd943','#EA8ADD','#3ec9d6','#F9152C'],
        plotOptions: {
          bar: {
             
            columnWidth: '50%',
            distributed: true,
          }
        },
        dataLabels: {
          enabled: false
        },
        legend: {
          show: true
        },
        xaxis: {
          categories: {!! json_encode($arrProcess_Label_priority) !!},
          labels: {
         
          }
        }
        };

        var chart = new ApexCharts(document.querySelector("#chart_priority"), options);
        chart.render();



///=====================Hour Chart =============================================================///

          
 var options = {
          series: [{
           data: [{!! json_encode($esti_logged_hour_chart) !!},{!! json_encode($logged_hour_chart) !!}],
         
        }],
          chart: {
          height: 210,
          width:300,
          type: 'bar',
        },
        colors: ['#963aff','#ffa21d'],
        plotOptions: {
          bar: {
               horizontal: true,
            columnWidth: '30%',
            distributed: true,
          }
        },
        dataLabels: {
          enabled: false
        },
        legend: {
          show: true
        },
        xaxis: {
          categories: ["Estimated Hours","Logged Hours "],
     
        }
        };

        var chart = new ApexCharts(document.querySelector("#chart-hours"), options);
        chart.render();
      
</script>
@endpush

