<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTaskTimer extends Model
{
    protected $table = 'project_task_timers';

    protected $fillable = [
        'task_id',
        'start_time',
        'end_time',
    ];

    public function taskTime($startTime, $endTime)
    {
        $totalTime      = strtotime($endTime) - strtotime($startTime);
        $minut          = ($totalTime) / 60;
        $hours          = (int)$minut / 60;
        $minutes        = $minut % 60;
        $totalTaskhours = ($hours > 1) ? (int)$hours . " hrs" . ' ' . (int)$minutes . " min" : "$minutes Minutes";

        return $totalTaskhours;
    }
}
