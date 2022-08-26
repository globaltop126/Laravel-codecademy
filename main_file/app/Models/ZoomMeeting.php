<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoomMeeting extends Model
{
    protected $fillable = [
          'title',
          'meeting_id',
          'client_id',
          'project_id',
          'start_date',
          'duration',
          'start_url',
          'password',
          'join_url',
          'status',
          'created_by',
    ];
    protected $appends  = array(
        'client_name',
        'project_name',
    );
    public function getClientNameAttribute($value)
    {
        $client = User::select('id', 'name')->where('id', $this->client_id)->first();

        return $client ? $client->name : '';
    }
    public function getUserNameAttribute($value)
    {
        $user = User::select('id', 'name')->where('id', $this->user_id)->first();

        return $user ? $user->name : '';
    }


    public function checkDateTime(){
        $m = $this;
        if (\Carbon\Carbon::parse($m->start_date)->addMinutes($m->duration)->gt(\Carbon\Carbon::now())) {
            return 1;
        }else{
            return 0;
        }
    }

    public function projectUser()
    {
        return ZoomMeeting::select('zoom_meetings.*', 'users.name')->join('users', 'users.id', '=', 'zoom_meetings.user_id')->where('project_id', '=', $this->id)->whereNotIn('user_id', [$this->created_by])->get();
    }

    public function projectName()
    {
        return $this->hasOne('App\Models\Project', 'id', 'project_id');
    }
    public function projectUsers()
    {
        return $this->hasOne('App\Models\User', 'id', 'employee');
    }
    public function projectClient()
    {
        return $this->hasOne('App\Models\User', 'id', 'client_id');
    }

    public function users($users)
    {

        $userArr = explode(',', $users);
        $users  = [];
        foreach($userArr as $user)
        {
            $users[] = User::find($user);
        }
        return $users;
    }

    public function clients($users)
    {
        $userArr = explode(',', $users);
        $users  = [];
        foreach($userArr as $user)
        {
            $users[] = User::find($user);
        }
        return $users;
    }
    
}