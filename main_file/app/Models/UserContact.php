<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserContact extends Model
{
    protected $fillable = [
        'parent_id',
        'user_id',
        'role',
    ];

    public function client()
    {
        return $this->hasOne('App\Models\User', 'name', 'id');
    }
    
     public static function user($client)
        {
            $categoryArr  = explode(',', $client);
            $unitRate = 0;
            foreach($categoryArr as $client)
            {
                $client          = User::find($client);
                $unitRate        = $client->name;
                
            }
            return $unitRate;
        }
}
