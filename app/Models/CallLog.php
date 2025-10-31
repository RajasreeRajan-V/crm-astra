<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'agent_id',
        'call_time',
        'duration',
        'notes',
        'outcome',
        'company_id'
    ];
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
    
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
     public function company()
    {
    return $this->belongsTo(Company::class);
    }
}
