<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    //
    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'is_calculated_by_experience',
        'default_days',
        'allows_carry_forward',
    ];
}
