<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractNote extends Model
{
    protected $table = 'contract_notes';

    protected $fillable = [
        'contract_id',
        'user_id',
        'notes',
        'created_by',
    ];
}
