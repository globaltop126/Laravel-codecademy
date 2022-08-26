<?php

namespace App\Http\Controllers;

use App\Models\project_report;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\ProjectEmailTemplate;
use App\Models\ProjectTask;
use App\Models\ProjectTaskTimer;
use App\Models\ProjectUser;
use App\Models\TaskStage;
use App\Models\Timesheet;
use App\Models\User;
use App\Models\UserContact;
use App\Exports\task_reportExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProjectReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            $usr           = Auth::user();
            $user_projects = $usr->projects()->pluck('permission', 'project_id')->toArray();
            $projects = Project::whereIn('id', array_keys($user_projects))->get();


              $project_status = Project::whereIn('id', array_keys($user_projects))->groupBy('status')->get();


              $usr_contact = $usr->contacts();
              $usr_contact = $usr_contact->pluck('user_id')->toArray();
              $users       = User::whereIn('id', $usr_contact);
              $users      = $users->where('id', '!=', $usr->id)->get();
           
             return view('project_report.index', compact('project_status','users' ,'projects'));


    }


 public function ajax_data(Request $request)
    {
            $usr           = Auth::user();
            $user_projects = $usr->projects()->pluck('permission', 'project_id')->toArray();
            $projects = Project::whereIn('id', array_keys($user_projects));


         if ($request->all_users) {
            unset($projects);
             $projects = Project::select('projects.*')->join('project_users', 'projects.id', '=', 'project_users.project_id')->where('project_users.user_id', '=', $request->all_users);
        }


         if ($request->name) {
            $projects->where('name', '=', $request->name);
        }
       
        if ($request->status) {
            $projects->where('status', '=', $request->status);
        }
      
        if ($request->start_date) {

             $projects->where('start_date', '=', $request->start_date);

        }

         if ($request->end_date) {

             $projects->where('end_date', '=', $request->end_date);

        }

      
        $projects = $projects->get();
        $data = [];
        foreach($projects as $project) {
            $tmp = [];
           
            $tmp['id'] = $project->id;
            // $tmp['name'] = $project->name;


                if($project->is_active == 1){
                                          


                                    $tmp['name'] = 
                                       '
                                        <a  class="name mb-0 h6 text-sm" data-toggle="popover"  title="' . __('view Project') . '" data-size="lg" data-title="' . __('show') . '" href="' . route(
                                            'projects.show',
                                                $project->id
                                            
                                        ) . '">'.$project->name.'</a>';





                                        }



                                        else{


                                               $tmp['name'] = 
                                       '
                                         <a>'.$project->name.'</a>';

                                        }

            $tmp['start_date'] = $project->start_date;
             $tmp['end_date'] = $project->end_date;

             $tmp['members'] = '<div class="avatar-group hover-avatar-ungroup mx-2" id="project_{{ $project->id }}">';
              if(isset($project->users) && !empty($project->users) && count($project->users) > 0){
             
                     foreach($project->users as $key => $user){
                           
                         $images = (count($project->users)-3) ;
                            

                                if($key < 3){
                                           $tmp['members'] .= 
                                          '
                                         
                                                 <a href="#" class="avatar rounded-circle avatar-sm" data-toggle="tooltip" data-placement="top" title=" '.$user->name.'">
                                                    <img '.$user->img_avatar.'/>

                                               
                                                  
                                                </a> ';
                                              

                                            }
                                           else{
                                            break;
                                           } 
                                        }

                           if(count($project->users) > 3){
                                           $tmp['members'] .= 
                                          '
                                         
                                                 <a href="#" class="avatar rounded-circle avatar-sm" data-toggle="tooltip" data-placement="top">
                                                    <img avatar="+ '.$images.'"/>

                                               
                                                  
                                                </a> ';
                                              

                                            }



                                      $tmp['members'] .=   '</div>';


}


                    $percentage = $project->project_progress();  



           $tmp['Progress'] = 
                '<div class="progress_wrapper">
                                       <div class="progress">
                                          <div class="progress-bar" role="progressbar" 
                                           style="width:'.$percentage["percentage"].'"
                                             aria-valuenow="55" aria-valuemin="0" aria-valuemax="100">
                                             </div>
                                       </div>
                                       <div class="progress_labels">
                                          <div class="total_progress">
                                          
                                             <strong>'.$percentage["percentage"].'</strong>
                                          </div>
                                     
                                       </div>
                                    </div>';



           
                $tmp['status'] = '<span class="badge badge-pill badge-'.Project::$status_color[$project->status].'">' .Project::$status[$project->status]. '</span>';
         

   
                $tmp['action'] = '
                <a  class=" actions action-item px-2" data-toggle="popover"  title="' . __('view Project') . '" data-size="lg" data-title="' . __('show') . '" href="' . route(
                   'project_report.show', 
                      
                        $project->id
                    
                ) . '"><i class="fas fa-eye"></i></a>



                <a  class="actions action-item px-2" data-toggle="popover"  title="' . __('Edit Project') . '"  data-size="lg" data-title="' . __('Edit') . '" href="' . route(
                    'projects.edit', 
                       
                        $project->id
                    
                ) . '"><i class="fas fa-edit"></i></a>';
           


           
            
            $data[] = $tmp;


     
        }

        return response()->json(['data' => $data], 200);
    }






    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }


       
     
public function show(Request $request ,$id)
 {
     $objUser = Auth::user();
      $project = Project::where('id', '=', $id)->first();
       
        if ($project) {
            
            $daysleft = round((((strtotime($project->end_date) - strtotime(date('Y-m-d'))) / 24) / 60) / 60);

            $project_status_task = TaskStage::join("project_tasks", "project_tasks.stage_id", "=", "task_stages.id")->where("task_stages.created_by", "=", $objUser->id)->where('project_tasks.project_id', '=', $id)->groupBy('task_stages.name')->selectRaw('count(project_tasks.id) as count, task_stages.name')->pluck('count', 'task_stages.name');

            $totaltask = ProjectTask::where('project_id',$id)->count();


           

             $arrProcessPer_status_task = [];
            $arrProcess_Label_status_tasks = [];
                foreach ($project_status_task as $lables => $percentage_stage) {
                     $arrProcess_Label_status_tasks[] = $lables;
                    if ($totaltask == 0) {
                        $arrProcessPer_status_task[] = 0.00;
                    } else {
                        $arrProcessPer_status_task[] = round(($percentage_stage * 100) / $totaltask, 2);
                    }
                }

           $project_priority_task = ProjectTask::where('project_id',$id)->groupBy('priority')->selectRaw('count(id) as count, priority')->pluck('count', 'priority');



            $arrProcessPer_priority = [];
            $arrProcess_Label_priority = [];
                foreach ($project_priority_task as $lable => $process) {
                     $arrProcess_Label_priority[] = $lable;
                    if ($totaltask == 0) {
                        $arrProcessPer_priority[] = 0.00;
                    } else {
                        $arrProcessPer_priority[] = round(($process * 100) / $totaltask, 2);
                    }
                }

                 $arrProcessClass = [
                    'text-success',
                    'text-primary',
                    'text-danger',
                ];
                   
              $stages = TaskStage::where('created_by', '=', $objUser->id)->where('project_id',$id)->groupBy('name')->orderBy('order')->get();
              

            $users    = $project->users()->get();

              $milestones = Milestone::where('project_id' ,$id)->get();

                //Logged Hours 
                       $logged_hour_chart = 0;
                       $total_hour = 0;
                       $logged_hour = 0;


                        $tasks = ProjectTask::where('project_id',$id)->get();

                        $data = [];
                        foreach ($tasks as $task) {
                            $timesheets_task = Timesheet::where('task_id',$task->id)->where('project_id',$id)->get(); 
                               

                        foreach($timesheets_task as $timesheet){
                                                           
                          $date_time = $timesheet->time;
                          $hours =  date('H', strtotime($date_time));
                          $minutes =  date('i', strtotime($date_time));
                          $total_hour = $hours + ($minutes/60) ;
                          $logged_hour += $total_hour ;

                          $logged_hour_chart = number_format($logged_hour, 2, '.', '');
                        }
                    }
                    //Estimated Hours
         

                 $esti_logged_hour_chart = ProjectTask::where('project_id',$id)->sum('estimated_hrs');


                 return view('project_report.show', compact( 'project', 'daysleft','arrProcessPer_priority','arrProcess_Label_priority','arrProcessClass','stages','users','milestones','arrProcess_Label_status_tasks','arrProcessPer_status_task','logged_hour_chart','esti_logged_hour_chart'));


                } else {
                    return redirect()->back()->with('error', __('Permission denied.'));
                }
     
    }

    public function edit(project_report $project_report)
    {
    
    }

   
    public function update(Request $request, project_report $project_report)
    {
    
    }

    public function destroy(project_report $project_report)
    {
        
    }

  
        public function export($id)
            {

                $name = 'task_report_' . date('Y-m-d i:h:s');
                $data = Excel::download(new task_reportExport($id), $name . '.xlsx');

                return $data;
            }


      public function ajax_tasks_report(Request $request ,$id)
    {
        $userObj = Auth::user();
      
        
        $usr_contact = UserContact::where('user_id', $userObj->id)->where('role','client')->first();

 
        if($usr_contact != '' && $usr_contact != null){


            $tasks = ProjectTask::select(
                [
                    'project_tasks.*',
                    'task_stages.name as stage',
                    'task_stages.complete',
                ]
            )->where('project_tasks.project_id',$id)->join("task_stages", "task_stages.id", "=", "project_tasks.stage_id")->whereRaw("find_in_set('" . $userObj->id . "',assign_to)");

        }

        else{


            $tasks = ProjectTask::select(
                [
                    'project_tasks.*',
                    'task_stages.name as stage',
                    'task_stages.complete',
                ]
            )->where('project_tasks.project_id',$id)->join("task_stages", "task_stages.id", "=", "project_tasks.stage_id")->where('project_tasks.created_by',$userObj->id);

           }
  
       
       
        if ($request->assign_to) {
            $tasks->whereRaw("find_in_set('" . $request->assign_to . "',assign_to)");
        }

        if ($request->priority) {
            $tasks->where('priority', '=', $request->priority);
        }

         if ($request->milestone_id) {
            $tasks->where('milestone_id', '=', $request->milestone_id);
        }

        
        if ($request->status) {
            $tasks->where('project_tasks.stage_id', '=', $request->status);
        }

         if ($request->start_date) {
            $tasks->where('start_date', '=', $request->status);
        }

        if ($request->due_date) {
            $tasks->where('end_date', '=', $request->due_date);
        }

        $tasks = $tasks->get();
          
        $data = [];
        foreach ($tasks as $task) {
            $timesheets_task = Timesheet::where('project_id',$id)->where('task_id' ,$task->id)->get(); 
            $hour_format_number = 0;
            $logged_hour = 0;
            $total_hour = 0;  
        foreach($timesheets_task as $timesheet){
          

          $date_time = $timesheet->time;
          $hours =  date('H', strtotime($date_time));
          $minutes =  date('i', strtotime($date_time));
          $total_hour = $hours + ($minutes/60) ;
          $logged_hour += $total_hour ;
          $hour_format_number = number_format($logged_hour, 2, '.', '');


        }
            
            $tmp = [];
            $tmp['name'] = '<a href="' . route(
                'projects.tasks.index', $task->project_id) . '" class="text-body">' . $task->name . '</a>';
          
            $tmp['milestone_id'] = ($milestone = $task->milestones()) ? $milestone->title : '';
             $start_date = '<span class="text-body">' . date('Y-m-d', strtotime($task->start_date)) . '</span> ';

            $due_date = '<span class="text-' . ($task->end_date < date('Y-m-d') ? 'danger' : 'success') . '">' . date('Y-m-d', strtotime($task->end_date)) . '</span> ';
            $tmp['start_date'] = $start_date;
            $tmp['end_date'] = $due_date;

            
                $tmp['user_name'] = "";
                foreach ($task->users() as $user) {
                    if (isset($user) && $user) {
                        $tmp['user_name'] .= '<span class="badge bg-secondary p-2 px-3 rounded">' . $user->name . '</span> ';
                    }
                }
            
             $tmp['logged_hours'] = $hour_format_number;


           
            if ($task->priority == "high") {
                $tmp['priority'] = '<span class="badge badge-pill badge-warning">' . __('High') . '</span>';
            } elseif ($task->priority == "critical") {
                $tmp['priority'] = '<span class="badge badge-pill badge-danger">' . __('Critical') . '</span>';
            } 

            elseif ($task->priority == "medium") {
                $tmp['priority'] = '<span class="badge badge-pill badge-success">' . __('Medium') . '</span>';
            }
            else {
                $tmp['priority'] = '<span class="badge badge-pill badge-info">' . __('Low') . '</span>';
            }

             if ($task->stage == 'Done') {
                $tmp['status'] = '<span class="badge badge-pill badge-success">' . __($task->stage) . '</span>';
            }

             elseif($task->stage == 'Todo') {
                $tmp['status'] = '<span class="badge badge-pill badge-primary">' . __($task->stage) . '</span>';
            }

             elseif($task->stage == 'Review') {
                $tmp['status'] = '<span class="badge badge-pill badge-danger">' . __($task->stage) . '</span>';
            }


             else {
                $tmp['status'] = '<span class="badge badge-pill badge-info">' . __($task->stage) . '</span>';
            }
         

            $data[] = $tmp;

        }

        return response()->json(['data' => $data], 200);

}







}
