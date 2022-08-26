<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'name',
        'client',
        'project',
        'subject',
        'value',
        'type',
        'start_date',
        'end_date',
        'notes',
        'description',
        'client_signature',
        'owner_signature',
        'created_by',
        'status',
    ];    

    public function ContractType()
    {
        return $this->hasOne('App\Models\ContractType', 'id', 'type');
    } 

    public function getprojectname()
    {
        return $this->hasOne('App\Models\Project', 'id', 'project');
    }

    public function clientdetail()
    {
        return $this->hasOne('App\Models\User', 'id', 'client');
    } 
   

    public function comment()
    {
        return $this->hasMany('App\Models\ContractComment', 'contract_id', 'id');
    }
    public function note()
    {
        return $this->hasMany('App\Models\ContractNote', 'contract_id', 'id');
    } 

    public function taskFiles()
    {
        return $this->hasMany('App\Models\ContractAttechment', 'contract_id' , 'id');
        // dd($this);
    }

    public static function getContractSummary($contracts)
    {
        $total = 0;

        foreach($contracts as $contract)
        {
            $total += $contract->value;
        }

        return \Auth::user()->priceFormat($total);
    }


}
