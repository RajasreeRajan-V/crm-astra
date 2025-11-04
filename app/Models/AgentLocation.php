<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentLocation extends Model
{
    protected $table = 'agent_locations';
    protected $fillable = [
        'agent_id', 'latitude', 'longitude', 'accuracy', 'location_time', 'user_agent', 'ip', 'tracking_status'
    ];

    protected $dates = ['location_time', 'created_at', 'updated_at'];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
