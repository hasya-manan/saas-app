<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveTier extends Model
{
    //
    protected $fillable = [
    'tenant_id', 'leave_type_id', 'min_years', 'max_years', 
    'allowed_days', 'max_carry_forward_days'
];

    public function leaveType()
        {
            return $this->belongsTo(LeaveType::class);
        }
}
