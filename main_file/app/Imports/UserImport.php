<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;

class UserImport implements ToModel
{
     use Importable; 

    public function model(array $row)
    {
       //
    }
}
