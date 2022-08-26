\<?php

namespace App\Imports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;

class ProjectImport implements ToModel
{
    use Importable; 

    public function model(array $row)
    {
        //
    }
}
