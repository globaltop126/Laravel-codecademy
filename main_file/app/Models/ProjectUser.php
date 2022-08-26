<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectUser extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'permission',
        'user_permission',
        'invited_by',
    ];

    // public function user($project)
    // {
    //     return $this->belongsToMany('App\Models\User', 'project_users', 'project_id', 'user_id')->withPivot('id','user_id','project_id', 'permission')->withTimestamps();
    //     // dd($data);
    // }
}
