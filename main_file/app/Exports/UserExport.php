<?php

namespace App\Exports;

use App\Models\User;
use App\Models\UserContact;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserExport implements FromCollection ,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = UserContact::where('parent_id' , \Auth::user()->id)->get();
        foreach($data as $k => $user)
        {
            $clients   = UserContact::user($user->user_id);
            $data[$k]["user_id"]   = $clients;
            unset($user->id,$user->created_at,$user->updated_at,$user->parent_id);

        }
        return $data;
    }

    public function headings(): array
    {
        return [
            "Name",
            "Type",
        ];
    }
}
