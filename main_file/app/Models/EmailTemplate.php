<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'name',
        'from',
        'keyword',
        'created_by',
    ];

    public function template($project_id)
    {
        return $this->hasOne('App\Models\ProjectEmailTemplate', 'template_id', 'id')->where('project_id', '=', $project_id)->first();
    }
}
