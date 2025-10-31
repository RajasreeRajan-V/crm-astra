<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Agent extends Authenticatable
{
    use Notifiable;
    protected $table = 'agents';
    protected $fillable = ['name', 'email','password','remember_token','phone_no','company_id'];
    
    public function leads()
    {
    return $this->hasMany(Lead::class, 'assigned_agent_id');
    }

    public function callLogs()
    {
    return $this->hasMany(CallLog::class, 'agent_id');
    }

    public function company()
    {
    return $this->belongsTo(Company::class);
    }

    public function locations()
    {
    return $this->hasMany(AgentLocation::class, 'agent_id');
    }

    public function routeNotificationFor($driver)
    {
        if ($driver === 'mail') {
            return $this->email; // Return the agent's email for mail notifications
        }

        return null; // Return null for other drivers
    }
}
