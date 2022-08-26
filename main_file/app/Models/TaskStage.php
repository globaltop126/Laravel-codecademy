<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskStage extends Model
{
    protected $fillable = [
        'name',
        'complete',
        'project_id',
        'order',
        'created_by',
    ];

    public static $stages = [
        "Todo",
        "In Progress",
        "Review",
        "Done",
    ];
}
