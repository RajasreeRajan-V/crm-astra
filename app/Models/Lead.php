<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lead extends Model
{
    use HasFactory;

    // Fillable fields for mass assignment
    protected $fillable = [
        'name',
        'contact',
        'source',
        'email',
        'status',
        'assigned_agent_id',
        'rate',
        'balance',
        'follow_up_date',
        'deal_item',
        'referral_id',
        'company_id'
    ];
    public function agent()
    {
        return $this->belongsTo(Agent::class, 'assigned_agent_id');
    }
    public function callLogs()
    {
    return $this->hasMany(CallLog::class, 'lead_id');
    }
    public function balanceUpdateLogs()
    {
        return $this->hasMany(BalanceUpdateLog::class);
    }
    public function referral()
    {
        return $this->belongsTo(Lead::class, 'referral_id');
    }
    public function company()
    {
    return $this->belongsTo(Company::class);
    }
    protected static function boot()
    {
    parent::boot();

    static::deleting(function ($lead) {
        // Delete associated call logs
        $lead->callLogs()->delete();
    });
    }
}
