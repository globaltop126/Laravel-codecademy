<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\EmailTemplate;
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
use App\Models\Utility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProjectExport;
use App\Imports\ProjectImport;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($view = 'grid')
    {
        $authuser = Auth::user();
        if($authuser->type != 'admin')
        {
            $allow = false;
            $plan  = $authuser->getPlan();
            if($plan)
            {
                $countProjects = $authuser->projects->count();
                if($countProjects < $plan->max_projects || $plan->max_projects == -1)
                {
                    $allow = true;
                }
            }

            return view('projects.index', compact('view', 'allow'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $plan = $user->getPlan();

        if($plan)
        {
            $countProjects = $user->projects->count();

            if($countProjects < $plan->max_projects || $plan->max_projects == -1)
            {
                $validation = [
                    'name' => 'required|max:120',
                    'currency' => 'required',
                ];

                if($request->hasFile('image'))
                {
                    $validation['image'] = 'mimes:jpeg,jpg,png';
                }

                $validator = Validator::make(
                    $request->all(), $validation
                );

                if($validator->fails())
                {
                    return redirect()->back()->with('error', $validator->errors()->first());
                }

                $project                      = new Project();
                $project['name']              = $request->name;
                $project['budget']            = (!empty($request->budget)) ? $request->budget : 0;
                $project['status']            = 'on_hold';
                $project['currency']          = $request->currency;
                $project['currency_code']     = 'USD';
                $project['currency_position'] = 'pre';
                $project['start_date']        = (!empty($request->start_date)) ? $request->start_date : null;
                $project['end_date']          = (!empty($request->end_date)) ? $request->end_date : null;
                $project['descriptions']      = $request->descriptions;
                $project['tags']              = $request->tags;
                $project['estimated_hrs']     = (!empty($request->estimated_hrs)) ? $request->estimated_hrs : 0;
                $project['created_by']        = $user->id;
                $project['is_active']         = 1;

                if($request->hasFile('image'))
                {
                    $fileNameToStore  = time() . '.' . $request->image->getClientOriginalExtension();
                    $path             = $request->file('image')->storeAs('projects', $fileNameToStore);
                    $project['image'] = $path;
                }

                $project->save();

                // Make Entry in project_users table
                ProjectUser::create(
                    [
                        'project_id' => $project->id,
                        'user_id' => $user->id,
                        'permission' => 'owner',
                        'user_permission' => json_encode(Auth::user()->getAllPermission()),
                    ]
                );

                // Make Entry in task_stages table
                foreach(TaskStage::$stages as $key => $value)
                {
                    TaskStage::create(
                        [
                            'name' => $value,
                            'complete' => (count(TaskStage::$stages) - 1 == $key) ? 1 : 0,
                            'project_id' => $project->id,
                            'order' => $key,
                            'created_by' => Auth::user()->id,
                        ]
                    );
                }

                // Make Entry In Project_Email_Template tbl
                $allEmail = EmailTemplate::all();
                foreach($allEmail as $email)
                {
                    ProjectEmailTemplate::create(
                        [
                            'template_id' => $email->id,
                            'project_id' => $project->id,
                            'is_active' => 1,
                        ]
                    );
                }

                $settings  = Utility::settingsById(Auth::user()->id);
               
                if(isset($settings['is_project_enabled']) && $settings['is_project_enabled'] == 1){
                    $msg = $project->name." created by the ".\Auth::user()->name.'.';
                    Utility::send_slack_msg($msg);    
                }

               
                if(isset($settings['telegram_is_project_enabled']) && $settings['telegram_is_project_enabled'] == 1){
                    $resp = $project->name ."created by the ".\Auth::user()->name.'.';
                    Utility::send_telegram_msg($resp);    
                }

                return redirect()->route('projects.index')->with('success', __('Project added successfully.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Your project limit is over, Please upgrade plan.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Default plan is deleted.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Project $project
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        if(Auth::user()->type != 'admin' && !empty($project))
        {
            $usr           = Auth::user();
            $user_projects = $usr->projects->pluck('id')->toArray();

            if(in_array($project->id, $user_projects) && $project->is_active == 1)
            {
                $project_data = [];
                // Task Count
                $project_task         = $project->tasks->count();
                $project_done_task    = $project->tasks->where('is_complete', '=', 1)->count();
                $project_data['task'] = [
                    'total' => number_format($project_task),
                    'done' => number_format($project_done_task),
                    'percentage' => Utility::getPercentage($project_done_task, $project_task),
                ];
                // end Task Count

                // Expense
                $expAmt = 0;
                foreach($project->expense as $expense)
                {
                    $expAmt += $expense->amount;
                }
                $project_data['expense'] = [
                    'allocated' => $project->budget,
                    'total' => $expAmt,
                    'percentage' => Utility::getPercentage($expAmt, $project->budget),
                ];
                // end expense

                // Users Assigned
                // $total_users                   = User::where('created_by', '=', $usr->id)->count();
                $total_users                   = $usr->contacts->count();
                $project_user                  = $project->users()->where('user_id', '!=', $usr->id)->count();
                $project_data['user_assigned'] = [
                    'total' => number_format($project_user) . '/' . number_format($total_users),
                    'percentage' => Utility::getPercentage($project_user, $total_users),
                ];
                // end users assigned

                // Day left
                $total_day                = Carbon::parse($project->start_date)->diffInDays(Carbon::parse($project->end_date));
                $remaining_day            = Carbon::parse($project->start_date)->diffInDays(now());
                $project_data['day_left'] = [
                    'day' => number_format($remaining_day) . '/' . number_format($total_day),
                    'percentage' => Utility::getPercentage($remaining_day, $total_day),
                ];
                // end Day left

                // Open Task
                if($usr->checkProject($project->id) == 'Owner')
                {
                    $remaining_task = ProjectTask::where('project_id', '=', $project->id)->where('is_complete', '=', 0)->count();
                    $total_task     = ProjectTask::where('project_id', '=', $project->id)->count();
                }
                else
                {
                    $remaining_task = ProjectTask::where('project_id', '=', $project->id)->where('is_complete', '=', 0)->whereRaw("find_in_set('" . $usr->id . "',assign_to)")->count();
                    $total_task     = ProjectTask::where('project_id', '=', $project->id)->whereRaw("find_in_set('" . $usr->id . "',assign_to)")->count();
                }
                $project_data['open_task'] = [
                    'tasks' => number_format($remaining_task) . '/' . number_format($total_task),
                    'percentage' => Utility::getPercentage($remaining_task, $total_task),
                ];
                // end open task

                // Milestone
                $total_milestone           = $project->milestones()->count();
                $complete_milestone        = $project->milestones()->where('status', 'LIKE', 'complete')->count();
                $project_data['milestone'] = [
                    'total' => number_format($complete_milestone) . '/' . number_format($total_milestone),
                    'percentage' => Utility::getPercentage($complete_milestone, $total_milestone),
                ];
                // End Milestone

                // Time spent
                if($usr->checkProject($project->id) == 'Owner')
                {
                    $times = $project->timesheets->pluck('time')->toArray();
                }
                else
                {
                    $times = $project->timesheets()->where('created_by', '=', $usr->id)->pluck('time')->toArray();
                }
                $totaltime                  = str_replace(':', '.', Utility::timeToHr($times));
                $estimatedtime              = $project->estimated_hrs != '' ? $project->estimated_hrs : '0';
                $project_data['time_spent'] = [
                    'total' => number_format($totaltime) . '/' . number_format($estimatedtime),
                    'percentage' => Utility::getPercentage(number_format($totaltime), $estimatedtime),
                ];
                // end time spent

                // Allocated Hours
                $hrs                                = Project::projectHrs($project->id);
                $project_data['task_allocated_hrs'] = [
                    'hrs' => number_format($hrs['allocated']) . '/' . number_format($hrs['total']),
                    'percentage' => Utility::getPercentage($hrs['allocated'], $hrs['total']),
                ];
                // end allocated hours

                // Chart
                $seven_days      = Utility::getLastSevenDays();
                $chart_task      = [];
                $chart_timesheet = [];
                $cnt             = 0;
                $cnt1            = 0;

                foreach(array_keys($seven_days) as $k => $date)
                {
                    if($usr->checkProject($project->id) == 'Owner')
                    {
                        $task_cnt     = $project->tasks()->where('is_complete', '=', 1)->where('marked_at', 'LIKE', $date)->count();
                        $arrTimesheet = $project->timesheets()->where('date', 'LIKE', $date)->pluck('time')->toArray();
                    }
                    else
                    {
                        $task_cnt     = $project->tasks()->where('is_complete', '=', 1)->whereRaw("find_in_set('" . $usr->id . "',assign_to)")->where('marked_at', 'LIKE', $date)->count();
                        $arrTimesheet = $project->timesheets()->where('created_by', '=', $usr->id)->where('date', 'LIKE', $date)->pluck('time')->toArray();
                    }

                    // Task Chart Count
                    $cnt += $task_cnt;

                    // Timesheet Chart Count
                    $timesheet_cnt = str_replace(':', '.', Utility::timeToHr($arrTimesheet));
                    $cn[]          = $timesheet_cnt;
                    $cnt1          += number_format($timesheet_cnt, 2);

                    $chart_task[]      = $task_cnt;
                    $chart_timesheet[] = number_format($timesheet_cnt, 2);
                }

                $project_data['task_chart']      = [
                    'chart' => $chart_task,
                    'total' => $cnt,
                ];
                $project_data['timesheet_chart'] = [
                    'chart' => $chart_timesheet,
                    'total' => $cnt1,
                ];

                // end chart

                return view('projects.show', compact('project', 'project_data'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Project $project
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        if(!empty($project))
        {
            if(Auth::user()->type != 'admin' && $project->is_active == 1)
            {
                $permissions = Auth::user()->getPermission($project->id);

                if(isset($permissions) && in_array('project setting', $permissions))
                {
                    $project->taskstages = $project->taskstages()->orderBy('order')->get()->toArray();
                    $EmailTemplates      = EmailTemplate::all();
                    //dd($project);
                    return view('projects.edit', compact('project', 'EmailTemplates'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Permission Denied.'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Project $project
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        $permissions = Auth::user()->getPermission($project->id);

        if(isset($permissions) && in_array('project setting', $permissions))
        {
            // validation
            $validation = [];
            if($request->from == 'basic')
            {
                $validation = [
                    'name' => 'required|max:120|unique:projects,name,' . $project->id,
                    'start_date' => 'required|date',
                    'end_date' => 'required|date',
                ];
            }
            elseif($request->from == 'financial')
            {
                $validation = [
                    'budget' => 'required|numeric',
                    'currency' => 'required',
                    'currency_code' => 'required',
                    'currency_position' => 'required',
                    'estimated_hrs' => 'required|numeric|min:0',
                ];
            }

            if($request->hasFile('image'))
            {
                $validation['image'] = 'mimes:jpeg,jpg,png';
            }

            $validator = Validator::make($request->all(), $validation);

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $user = Auth::user();
            if($request->from == 'basic')
            {
                $project['name']         = $request->name;
                $project['status']       = $request->status;
                $project['start_date']   = $request->start_date;
                $project['end_date']     = $request->end_date;
                $project['descriptions'] = $request->descriptions;

                if($request->hasFile('image'))
                {
                    Utility::checkFileExistsnDelete([$project->image]);

                    $fileNameToStore  = time() . '.' . $request->image->getClientOriginalExtension();
                    $path             = $request->file('image')->storeAs('projects', $fileNameToStore);
                    $project['image'] = $path;
                }
            }
            elseif($request->from == 'financial')
            {
                $project['budget']            = $request->budget;
                $project['currency']          = $request->currency;
                $project['currency_code']     = $request->currency_code;
                $project['currency_position'] = $request->currency_position;    
                $project['estimated_hrs']     = $request->estimated_hrs;
                $project['tags']              = $request->tags;
            }
            elseif($request->from == 'project_progress')
            {
                $project['project_progress'] = $request->project_progress;
                if(isset($request->progress))
                {
                    $project['progress'] = $request->progress;
                }
            }
            elseif($request->from = 'task_progress_bar')
            {
                $project['task_progress'] = $request->task_progress;
            }

            $project->save();

            return redirect()->back()->with('success', __('Project updated successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Project $project
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        if($project->is_active == 1)
        {
            Project::deleteProject($project->id);

            return redirect()->route('projects.index')->with('success', __('Project deleted successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    // For Filter
    public function filterProjectView(Request $request)
    {
        $usr           = Auth::user();
        $user_projects = $usr->projects()->pluck('permission', 'project_id')->toArray();    
        // dd($user_projects);
        if($request->ajax() && $request->has('view') && $request->has('sort'))
        {
            $sort     = explode('-', $request->sort);
            $projects = Project::whereIn('id', array_keys($user_projects))->orderBy($sort[0], $sort[1]);
            // dd($projects );
            if(!empty($request->keyword))
            {
                $projects->where('name', 'LIKE', $request->keyword . '%')->orWhereRaw('FIND_IN_SET("' . $request->keyword . '",tags)');
            }
            if(!empty($request->status))
            {
                $projects->whereIn('status', $request->status);
            }
            $projects   = $projects->get();
           
            $returnHTML = view('projects.' . $request->view, compact('projects', 'user_projects'))->render();
           
            return response()->json(
                [
                    'success' => true,
                    'html' => $returnHTML,
                ]
            );
        }
    }

    // For Load User on Project Detail Page
    public function loadUser(Request $request)
    {
        if($request->ajax())
        {
            $project    = Project::find($request->project_id);
            $returnHTML = view('projects.users', compact('project'))->render();

            return response()->json(
                [
                    'success' => true,
                    'html' => $returnHTML,
                ]
            );
        }
    }

    // For Invite Member
    public function inviteMemberView(Request $request, $project_id)
    {
        $usr          = Auth::user();
        $project      = Project::find($project_id);
        $user_project = $project->users->pluck('id')->toArray();

        $user_contact = UserContact::where('parent_id', '=', $usr->id)->where('role', 'user')->whereNOTIn('user_id', $user_project)->pluck('user_id')->toArray();
        $arrUser      = array_unique($user_contact);
        $users        = User::whereIn('id', $arrUser)->get();

        $client_contact = UserContact::where('parent_id', '=', $usr->id)->where('role', 'client')->whereNOTIn('user_id', $user_project)->pluck('user_id')->toArray();
        $arrClient      = array_unique($client_contact);
        $clients        = User::whereIn('id', $arrClient)->get();

        return view('projects.invite', compact('project_id', 'users', 'clients'));
    }

    public function inviteProjectUserMember(Request $request)
    {
        $authuser = Auth::user();
        $role     = $request->role;

        // check validation
        $validator = Validator::make(
            $request->all(), [
                               'username' => 'required',
                               'useremail' => 'required|email|unique:users,email',
                               'userpassword' => 'required',
                           ]
        );
        if($validator->fails())
        {
            return json_encode(
                [
                    'code' => 404,
                    'status' => 'Error',
                    'error' => __('The Email has already been taken.'),
                ]
            );
        }
        // end validation

        $plan = $authuser->getPlan();
        if($plan)
        {
            $name       = $request->username;
            $email      = $request->useremail;
            $password   = $request->userpassword;
            $project_id = $request->project_id;

            $countUsers = $authuser->contacts->count();
            if($countUsers < $plan->max_users || $plan->max_users == -1)
            {
                // make new user
                $user = User::create(
                    [
                        'name' => $name,
                        'email' => $email,
                        'password' => Hash::make($password),
                        'type' => 'user',
                        'created_by' => $authuser->id,
                        'lang' => Utility::getValByName('default_language'),
                        'is_active' => 1,
                        'is_invited' => 1,
                    ]
                );

                $user->assignPlan(1);

                // make entry in user_contact tbl
                UserContact::create(
                    [
                        'parent_id' => $authuser->id,
                        'user_id' => $user->id,
                        'role' => $role,
                    ]
                );

                // Make entry in project_user tbl
                ProjectUser::create(
                    [
                        'project_id' => $project_id,
                        'user_id' => $user->id,
                        'permission' => $role,
                        'user_permission' => json_encode($authuser->getAllPermission()),
                        'invited_by' => $authuser->id,
                    ]
                );

                // Make entry in activity_log tbl
                ActivityLog::create(
                    [
                        'user_id' => $authuser->id,
                        'project_id' => $project_id,
                        'log_type' => 'Invite User',
                        'remark' => json_encode(['title' => $user->name]),
                    ]
                );

                // send email
                $uArr = [
                    'email' => $email,
                    'password' => $password,
                ];
                $resp = Utility::sendEmailTemplate('User Invite', [$user->id => $user->email], $uArr);

                $project = Project::find($project_id);
                $pArr    = [
                    'project_name' => $project->name,
                    'project_status' => Project::$status[$project->status],
                    'project_budget' => Utility::projectCurrencyFormat($project->id, $project->budget),
                    'project_hours' => number_format($project->estimated_hrs),
                ];

                Utility::sendEmailTemplate('Invite Project', [$user->id => $user->email], $pArr, $project_id);

                return json_encode(
                    [
                        'code' => 200,
                        'status' => 'Success',
                        'success' => __('User invited successfully.') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''),
                    ]
                );
            }
            else
            {
                return json_encode(
                    [
                        'code' => 404,
                        'status' => 'Error',
                        'error' => __('Your user limit is over, Please upgrade plan.'),
                    ]
                );
            }
        }
        else
        {
            return json_encode(
                [
                    'code' => 404,
                    'status' => 'Error',
                    'error' => __('Default plan is deleted.'),
                ]
            );
        }
    }

    // For MileStones
    public function milestone($project_id)
    {
        $permissions = Auth::user()->getPermission($project_id);

        if(isset($permissions) && in_array('create milestone', $permissions))
        {
            $project = Project::find($project_id);

            return view('projects.milestone', compact('project'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function milestoneStore(Request $request, $project_id)
    {
        $permissions = Auth::user()->getPermission($project_id);

        if(isset($permissions) && in_array('create milestone', $permissions))
        {
            $project   = Project::find($project_id);
            $validator = Validator::make(
                $request->all(), [
                                   'title' => 'required',
                                   'status' => 'required',
                               ]
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $milestone              = new Milestone();
            $milestone->project_id  = $project->id;
            $milestone->title       = $request->title;
            $milestone->status      = $request->status;
            $milestone->description = $request->description;
            $milestone->save();

            ActivityLog::create(
                [
                    'user_id' => \Auth::user()->id,
                    'project_id' => $project->id,
                    'log_type' => 'Create Milestone',
                    'remark' => json_encode(['title' => $milestone->title]),
                ]
            );

            $settings  = Utility::settingsById(Auth::user()->id);
           
            if(isset($settings['mileston_notificaation']) && $settings['mileston_notificaation'] == 1){
                $msg =$project->name. " in New Milestone created by the ".\Auth::user()->name.'.';
                Utility::send_slack_msg($msg);    
            }
             if(isset($settings['telegram_mileston_notificaation']) && $settings['telegram_mileston_notificaation'] == 1){
                    $resp =$project->name. " in New Milestone created by the ".\Auth::user()->name.'.';
                    Utility::send_telegram_msg($resp);    
                }

            return redirect()->back()->with('success', __('Milestone successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function milestoneEdit($id)
    {
        $milestone = Milestone::find($id);

        return view('projects.milestoneEdit', compact('milestone'));
    }

    public function milestoneUpdate($id, Request $request)
    {
        $mileston=MileStone::where('id',$id)->first();
        $request->validate(
            [
                'title' => 'required',
                'status' => 'required',
            ]
        );

         if($mileston->status !=$request->status)
        {
            $status=1;
        }
        else
        {
            $status=0;
        }

        $milestone              = Milestone::find($id);
        $milestone->title       = $request->title;
        $milestone->status      = $request->status;
        $milestone->progress    = $request->progress;
        $milestone->description = $request->description;
        $milestone->start_date  = $request->start_date;
        $milestone->end_date     = $request->end_date;
        $milestone->save();

        $settings  = Utility::settingsById(Auth::user()->id);
        
        if($status==1)
         {
            if(isset($settings['milestone_status_notificaation']) && $settings['milestone_status_notificaation'] == 1){
                $msg = $milestone->title . ' status changed '.$milestone->status ;
                Utility::send_slack_msg($msg);    
            }

             if(isset($settings['telegram_milestone_status_notificaation']) && $settings['telegram_milestone_status_notificaation'] == 1){
                    $resp = $milestone->title . ' status changed '.$milestone->status ;
                    Utility::send_telegram_msg($resp);    
                }
                
         }

        

        return redirect()->back()->with('success', __('Milestone updated successfully.'));
    }

    public function milestoneDestroy($id)
    {
        $milestone = Milestone::find($id);
        $milestone->delete();

        return redirect()->back()->with('success', __('Milestone successfully deleted.'));
    }

    public function milestoneShow($id)
    {
        $milestone = Milestone::find($id);

        return view('projects.milestoneShow', compact('milestone'));
    }

    // Remove User from Project
    public function removeUserFromProject($project_id, $user_id)
    {
        $project = Project::find($project_id);
        $user    = User::find($user_id);

        if($project && $user)
        {
            // Remove from project_user tbl
            $project->users()->detach($user->id);

            // Delete From project_tasks Table
            ProjectTask::removeAssigned($user->id);

            return redirect()->back()->with('success', __('Member removed from this project.'));
        }

        return redirect()->back()->with('success', __('Member cannot be removed from this project.'));
    }

    // Move Task Stage
    public function storeProjectTaskStages(Request $request, $project_id)
    {
        $rules = [
            'stages' => 'required|present|array',
        ];

        $attributes = [];

        if($request->stages)
        {
            foreach($request->stages as $key => $val)
            {
                $rules['stages.' . $key . '.name']      = 'required|max:255';
                $attributes['stages.' . $key . '.name'] = __('Stage Name');
            }
        }

        $validator = Validator::make($request->all(), $rules, [], $attributes);

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $arrStages = TaskStage::where('project_id', '=', $project_id)->orderBy('order')->pluck('name', 'id')->all();
        $order     = 0;

        foreach($request->stages as $key => $stage)
        {
            $obj = new TaskStage();
            if(isset($stage['id']) && !empty($stage['id']))
            {
                $obj = TaskStage::find($stage['id']);
                unset($arrStages[$obj->id]);
            }
            $obj->project_id = $project_id;
            $obj->name       = $stage['name'];
            $obj->order      = $order++;
            $obj->complete   = 0;
            $obj->save();
        }

        if($arrStages)
        {
            foreach($arrStages as $id => $name)
            {
                TaskStage::find($id)->delete();
            }
        }
        $lastStage = TaskStage::where('project_id', '=', $project_id)->orderBy('order', 'desc')->first();

        if($lastStage)
        {
            $lastStage->complete = 1;
            $lastStage->save();
        }

        return redirect()->back()->with('success', __('Stages Saved Successfully.'));
    }

    // User Permission Module
    public function userPermission($project_id, $user_id)
    {
        $project     = Project::find($project_id);
        $user        = User::find($user_id);
        $permissions = $user->getPermission($project_id);

        if(!$permissions)
        {
            $permissions = [];
        }

        return view('projects.user_permission', compact('project', 'user', 'permissions'));
    }

    public function userPermissionStore($project_id, $user_id, Request $request)
    {
        $userProject                  = ProjectUser::where('project_id', '=', $project_id)->where('user_id', '=', $user_id)->first();
        $userProject->user_permission = json_encode($request->permissions);
        $userProject->save();

        return redirect()->back()->with('success', __('Permission Updated Successfully!'));
    }
    // end User permission module

    // Project Gantt Chart
    public function gantt($projectID, $duration = 'Week')
    {
        $project = Project::find($projectID);
        $tasks   = [];

        if($project)
        {
            $tasksobj = $project->tasks;

            foreach($tasksobj as $task)
            {
                $tmp                 = [];
                $tmp['id']           = 'task_' . $task->id;
                $tmp['name']         = $task->name;
                $tmp['start']        = $task->start_date;
                $tmp['end']          = $task->end_date;
                $tmp['custom_class'] = (empty($task->priority_color) ? '#ecf0f1' : $task->priority_color);
                $tmp['progress']     = str_replace('%', '', $task->taskProgress()['percentage']);
                $tmp['extra']        = [
                    'priority' => ucfirst(__($task->priority)),
                    'comments' => count($task->comments),
                    'duration' => Utility::getDateFormated($task->start_date) . ' - ' . Utility::getDateFormated($task->end_date),
                ];
                $tasks[]             = $tmp;
            }
        }

        return view('projects.gantt', compact('project', 'tasks', 'duration'));
    }

    public function ganttPost($projectID, Request $request)
    {
        $project = Project::find($projectID);

        if($project)
        {
            $permissions = Auth::user()->getPermission($projectID);

            if(isset($permissions) && in_array('show task', $permissions))
            {
                $id               = trim($request->task_id, 'task_');
                $task             = ProjectTask::find($id);
                $task->start_date = $request->start;
                $task->end_date   = $request->end;
                $task->save();

                return response()->json(
                    [
                        'is_success' => true,
                        'message' => __("Time Updated"),
                    ], 200
                );
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'message' => __("You can't change Date!"),
                    ], 400
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'message' => __("Something is wrong."),
                ], 400
            );
        }
    }

    public function export()
    {
        $name = 'Project' . date('Y-m-d i:h:s');
        $data = Excel::download(new ProjectExport(), $name . '.xlsx');

        return $data;
    }


    public function importFile()
    {
        return view('projects.import');
    }

    public function import(Request $request)
    {
        $user = Auth::user();
        $rules = [
            'file' => 'required',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
     

        $projects = (new ProjectImport())->toArray(request()->file('file'))[0];
        
        $totalitem = count($projects) - 1;
        $errorArray    = [];
        for($i = 1; $i <= count($projects) - 1; $i++)
        {
            $project = $projects[$i];

            $projectByEmail = Project::where('name', $project[1])->first();
            if(!empty($projectByEmail))
            {
                $projectData = $projectByEmail;
            }
            else
            {
                $projectData = new Project();
            }
            //$projectData->id                  = $project[0];
            $projectData->name                = $project[0];
            $projectData->status              = $project[1];
            $projectData->budget	          = 0;
            $projectData->start_date          = $project[3];
            $projectData->end_date            = $project[4];
            $projectData->currency            = $project[5];
            $projectData->currency_code       = 'USD';
            $projectData->currency_position   = 'pre';
            $projectData->descriptions        = $project[8];
            $projectData->project_progress    = 'false';
            $projectData->progress            = '0';
            $projectData->task_progress       = 'true';
            $projectData->is_active           = 1;
            $projectData->tags                = $project[12];
            $projectData->estimated_hrs       = $project[13];
            $projectData->created_by          = \Auth::user()->creatorId();
            if(empty($projectData))
            {
                $errorArray[]      = $projectData;
            }
            else
            {
                $projectData->save();
            }

              // Make Entry in project_users table
              ProjectUser::create(
                [
                    'project_id' => $projectData->id,
                    'user_id' => $user->id,
                    'permission' => 'owner',
                    'user_permission' => json_encode(Auth::user()->getAllPermission()),
                ]
            );

             // Make Entry in task_stages table
             foreach(TaskStage::$stages as $key => $value)
             {
                 TaskStage::create(
                     [
                         'name' => $value,
                         'complete' => (count(TaskStage::$stages) - 1 == $key) ? 1 : 0,
                         'project_id' => $projectData->id,
                         'order' => $key,
                         'created_by' => Auth::user()->id,
                     ]
                 );
             }

             // Make Entry In Project_Email_Template tbl
             $allEmail = EmailTemplate::all();
             foreach($allEmail as $email)
             {
                 ProjectEmailTemplate::create(
                     [
                         'template_id' => $email->id,
                         'project_id' => $projectData->id,
                         'is_active' => 1,
                     ]
                 );
             }

            
        }

        $errorRecord = [];
        if(empty($errorArray))
        {
            $data['status'] = 'success';
            $data['msg']    = __('Record successfully imported');
        }
        else
        {
            $data['status'] = 'error';
            $data['msg']    = count($errorArray) . ' ' . __('Record imported fail out of' . ' ' . $totalitem . ' ' . 'record');


            foreach($errorArray as $errorData)
            {

                $errorRecord[] = implode(',', $errorData);

            }

            \Session::put('errorArray', $errorRecord);
        }

        return redirect()->back()->with($data['status'], $data['msg']);
    }

}
