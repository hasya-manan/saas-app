<?php

namespace App\Models;

use App\Traits\HasCustomPagination;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveType extends Model
{
    use HasCustomPagination, SoftDeletes;
    
    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'is_calculated_by_experience',
        'default_days',
        'allows_carry_forward',
        'probation_period_months',
        'is_pro_rate',
    ];

    /**
     *  Hides it from log outputs, array dumps, and JSON strings
     * 
     */
    protected $hidden = [
        'tenant_id',
    ];
}
