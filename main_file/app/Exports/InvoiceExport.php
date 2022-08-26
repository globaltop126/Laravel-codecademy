<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;

class InvoiceExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = Invoice::get();
        foreach($data as $k => $project)
        {
            unset($project->id,$project->image,$project->created_by,$project->created_at,$project->updated_at);

        }
        return $data;
    }
}
