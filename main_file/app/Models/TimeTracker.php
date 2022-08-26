<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
use App\Models\User;
use App\Models\Utility;
use App\Models\Tag;
use App\Models\ProjectTask;

class TimeTracker extends Model
{
    //
    protected $fillable = [
        'project_id',
        'task_id',
        'is_active',
        'tag_id',
        'name',
        'is_billable',
        'start_time',
        'end_time',
        'total_time',
        'created_by',

    ];
    protected $appends  = array(
        'project_name',
        'title',
        'total',
        'user_name',
        'user_avatar',
        'project_task',
    );

    public function getProjectNameAttribute($value)
    {
        $project = Project::select('id', 'name')->where('id', $this->project_id)->first();

        return $project ? $project->name : '';
    }

    public function getUserNameAttribute($value)
    {
        $user = User::select('id', 'name')->where('id', $this->created_by)->first();

        return $user ? $user->name : '';
    }

    public function getTotalAttribute($value)
    {
        $total = Utility::second_to_time($this->total_time);

        return $total ? $total : '00:00:00';
    }

    public function getUserAvatarAttribute($value)
    {
        $user = User::select('id', 'avatar')->where('id', $this->created_by)->first();

        return $user ? asset(\Storage::url($user->avatar)) : asset('assets/images/avatar.png');
    }

    public function getTagsNameAttribute($value)
    {

        if($this->tag_id != '' && $this->tag_id != null)
        {
            $tag_ids = explode(',', $this->tag_id);
        }
        else
        {
            $tag_ids = '';
        }
        if(!empty($tag_ids))
        {
            $tags = Tag::whereIn('id', $tag_ids)->pluck('name');
            if(!empty($tags))
            {
                $tags = $tags;
            }
            else
            {
                $tags = '';
            }

            return $tags;
        }
        else
        {
            return '';
        }
    }

    public function getProjectTaskAttribute($value)
    {
        $project  = Project::where('id', $this->project_id)->first();
        $p_with_t = '';
        if($project->name && $project->name != '')
        {
            $p_with_t = $project->name;
        }
        if($this->task_id && $this->task_id !== '')
        {
            $task_name = ProjectTask::where('id', $this->task_id)->first();
            $p_with_t  .= $task_name = !empty($task_name) ? $task_name->title : '';
        }

        return $p_with_t;
    }

    public function getTitleAttribute($value)
    {
        $project       = Project::where('id', $this->project_id)->first();
        $project_color = '';
        $task_name     = '';
        $client_name   = '';
        $project_name  = '';
        if($project->name && $project->name != '')
        {
            $project_name = $project->name;
        }
        if($project->color)
        {
            $project_color = $project->color;
        }
        if($this->task_id && $this->task_id !== '')
        {
            $task_name = ProjectTask::where('id', $this->task_id)->first();
            $task_name = !empty($task_name) ? $task_name->title : '';
        }
        if($project->client_id && $project->client_id != '')
        {
            $client = User::select('id', 'name')->where('id', $project->client_id)->first();
            if($client)
            {
                $client_name = $client->name && $client->name != '' ? $client->name : '';
            }
        }
        $data = [
            'project_name' => $project_name,
            'project_color' => $project_color,
            'client_name' => $client_name,
            'task_name' => $task_name,
        ];

        return $data;
    }
    
    public function project()
    {
        return $this->hasOne('App\Models\Project', 'id', 'project_id');
    }
    public function tasks()
    {
        return $this->hasOne('App\Models\ProjectTask', 'id', 'task_id');
    }
}
