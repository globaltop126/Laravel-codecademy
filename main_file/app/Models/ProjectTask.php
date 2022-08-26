<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProjectTask extends Model
{
    protected $fillable = [
        'name',
        'description',
        'estimated_hrs',
        'start_date',
        'end_date',
        'priority',
        'priority_color',
        'assign_to',
        'project_id',
        'milestone_id',
        'stage_id',
        'order',
        'created_by',
        'is_favourite',
        'is_complete',
        'marked_at',
        'progress',
        'time_tracking'
    ];

    public static $priority = [
        'critical' => 'Critical',
        'high' => 'High',
        'medium' => 'Medium',
        'low' => 'Low',
    ];

    public static $priority_color = [
        'critical' => 'danger',
        'high' => 'warning',
        'medium' => 'primary',
        'low' => 'info',
    ];

    // Get task users
    public function users()
    {
        return User::whereIn('id', explode(',', $this->assign_to))->get();
    }

    // Get task based comments
    public function comments()
    {
        return $this->hasMany('App\Models\TaskComment', 'task_id', 'id')->orderBy('id', 'DESC');
    }

    // Get task based checklist
    public function checklist()
    {
        return $this->hasMany('App\Models\TaskChecklist', 'task_id', 'id')->orderBy('id', 'DESC');
    }

    // Get task based checklist "completed/total"
    public function countTaskChecklist()
    {
        return $this->checklist->where('status', '=', 1)->count() . '/' . $this->checklist->count();
    }

    // Get task based it's file
    public function taskFiles()
    {
        return $this->hasMany('App\Models\TaskFile', 'task_id', 'id')->orderBy('id', 'DESC');
    }

    // Get task based activity log
    public function activity_log()
    {
        return ActivityLog::where('user_id', '=', \Auth::user()->id)->where('project_id', '=', $this->project_id)->where('task_id', '=', $this->id)->get();
    }

    // Get Milestone that assigned in task
    public function milestone()
    {
        return $this->hasOne('App\Models\Milestone', 'id', 'milestone_id');
    }

        public function milestones()
    {
        return $this->milestone_id ? Milestone::find($this->milestone_id) : null;
    }

    public function timesheets()
    {
        return $this->hasMany('App\Models\Timesheet', 'task_id', 'id')->orderBy('id', 'desc');
    }

    // For delete task and it's sub record
    public static function deleteTask($task_ids)
    {
        $status = false;

        foreach($task_ids as $key => $task_id)
        {
            $task = ProjectTask::find($task_id);

            if($task)
            {
                // Delete Attachments
                $taskattachments = TaskFile::where('task_id', '=', $task->id);
                $attachmentfiles = $taskattachments->pluck('file')->toArray();
                Utility::checkFileExistsnDelete($attachmentfiles);
                $taskattachments->delete();

                // Delete Timesheets
                $task->timesheets()->delete();

                // Delete Checklists
                TaskChecklist::where('task_id', '=', $task->id)->delete();

                // Delete Comments
                TaskComment::where('task_id', '=', $task->id)->delete();

                // Delete Members
                // $task->taskmembers()->detach();

                // Delete Task
                $status = $task->delete();
            }
        }

        // echo json_encode($status);
        return true;
    }

    // Get task progress percentage
    public function taskProgress()
    {
        $project    = Project::find($this->project_id);
        $percentage = 0;

        if($project->task_progress == 'true')
        {
            $total_checklist     = $this->checklist->count();
            $completed_checklist = $this->checklist()->where('status', '=', '1')->count();

            if($total_checklist > 0)
            {
                $percentage = intval(($completed_checklist / $total_checklist) * 100);
            }
        }
        else
        {
            $percentage = $this->progress;
        }

        $color = Utility::getProgressColor($percentage);

        return [
            'color' => $color,
            'percentage' => $percentage . '%',
        ];
    }

    // Get task stage
    public function stage()
    {
        return $this->hasOne('App\Models\TaskStage', 'id', 'stage_id');
    }

    // Get task project
    public function project()
    {
        return $this->hasOne('App\Models\Project', 'id', 'project_id');
    }

    // Return milestone wise tasks
    public static function getAllSectionedTaskList($request, $project, $filterdata = [], $not_task_ids = [])
    {
        $taskArray    = $sectionArray = [];
        $counter      = 1;
        $taskSections = $project->tasksections()->pluck('title', 'id')->toArray();
        $section_ids  = array_keys($taskSections);
        $task_ids     = Project::getAssignedProjectTasks($project->id, null, $filterdata)->whereNotIn('milestone_id', $section_ids)->whereNotIn('id', $not_task_ids)->orderBy('id', 'desc')->pluck('id')->toArray();
        if(!empty($task_ids) && count($task_ids) > 0)
        {
            $counter                              = 0;
            $taskArray[$counter]['section_id']    = 0;
            $taskArray[$counter]['section_name']  = '';
            $taskArray[$counter]['sectionsClass'] = 'active';
            foreach($task_ids as $task_id)
            {
                $task                            = ProjectTask::find($task_id);
                $taskCollectionArray             = $task->toArray();
                $taskCollectionArray['taskinfo'] = json_decode(app('App\Http\Controllers\ProjectTaskController')->getDefaultTaskInfo($request, $task->id), true);

                $taskArray[$counter]['sections'][] = $taskCollectionArray;
            }
            $counter++;
        }
        if(!empty($section_ids) && count($section_ids) > 0)
        {
            foreach($taskSections as $section_id => $section_name)
            {
                $tasks                               = Project::getAssignedProjectTasks($project->id, null, $filterdata)->where('project_tasks.milestone_id', $section_id)->whereNotIn('id', $not_task_ids)->orderBy('id', 'desc')->get()->toArray();
                $taskArray[$counter]['section_id']   = $section_id;
                $taskArray[$counter]['section_name'] = $section_name;
                $sectiontasks                        = $tasks;

                foreach($tasks as $onekey => $onetask)
                {
                    $sectiontasks[$onekey]['taskinfo'] = json_decode(app('App\Http\Controllers\ProjectTaskController')->getDefaultTaskInfo($request, $onetask['id']), true);
                }

                $taskArray[$counter]['sections']      = $sectiontasks;
                $taskArray[$counter]['sectionsClass'] = 'active';
                $counter++;
            }
        }

        return $taskArray;
    }

    // Remove user from task
    public static function removeAssigned($user_id)
    {
        // Delete From Project Task Table
        $tasks = ProjectTask::where('created_by', '=', Auth::user()->id)->whereRaw("find_in_set('" . $user_id . "',assign_to)")->get();

        foreach($tasks as $task)
        {
            $assign_to = explode(',', $task->assign_to);
            foreach($assign_to as $k => $v)
            {
                if($v == $user_id)
                {
                    unset($assign_to[$k]);
                }
            }
            $task->assign_to = implode(',', $assign_to);
            $task->save();
        }
    }

    // Get Time Tracking Records by Task
    public function trackingRec()
    {
        return $this->hasMany('App\Models\ProjectTaskTimer', 'task_id', 'id')->orderBy('id', 'DESC');
    }

    // Get TaskTime Tracking
    public static function lastTime()
    {
        $return   = [];
        $userTask = ProjectTask::whereRaw("find_in_set('" . Auth::user()->id . "',assign_to)")->where('time_tracking', 1)->first();
        if(!empty($userTask))
        {
            $lastTime = ProjectTaskTimer::where('task_id', $userTask->id)->orderBy('id', 'desc')->first();

            $return['start_time'] = $lastTime->start_time;
            $return['title']      = $userTask->name . ' (' . $userTask->project->name . ')';
        }

        return $return;

    }
}
