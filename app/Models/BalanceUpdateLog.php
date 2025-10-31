<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BalanceUpdateLog extends Model
{
    protected $fillable = [
        'lead_id',
        'field_updated',
        'previous_value',
        'new_value',
        'notes',
        'updated_by',
        'updated_at',
        'company_id'
    ];

    // Define relationship with Lead
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    // Define relationship with User (updated by)
    public function updatedBy()
    {
        return $this->belongsTo(Agent::class, 'updated_by');
    }
}