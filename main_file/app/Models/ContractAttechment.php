<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractAttechment extends Model
{

    protected $fillable = [
        'contract_id',
        'user_id',
        'files',
        'created_by',
        'name',
        'extension',
        'file_size',
    ];  
}
