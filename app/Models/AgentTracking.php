<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentTracking extends Model
{
    use HasFactory;

    protected $table = 'agent_tracking';

    protected $fillable = ['company_id','last_assigned_agent_index'];
}
