<?php

namespace App\Exports;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use App\Models\ProjectUser;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProjectExport implements FromCollection ,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $usr           = Auth::user();
        $user_projects = $usr->projects()->pluck('permission', 'project_id')->toArray();  
        $projects = Project::whereIn('id', array_keys($user_projects))->get();
       
        foreach($projects as $k => $project)
        {
           
            $project_user            = $project->users->where('user_id', '!=', $usr->id)->pluck('name');
            $data[$k]["project_id"]  = $project_user;

            unset($project->id,$project->image,$project->created_by,$project->is_active,$project->created_at,$project->updated_at);

        }
        return $projects;
    }

    public function headings(): array
    {
        return [
            "Name",
            "Status",
            "Budget",
            "Start Date",
            "End Date",
            "Currency",
            "Currency Code",
            "Currency Position",
            "Description",
            "Project Progress",
            "Progress",
            "Task Progress",
            "Tags",
            "Estimated Hours",
            "Invited User"
        ];
    }
}
