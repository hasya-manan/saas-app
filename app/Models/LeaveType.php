<?php

namespace App\Models;

use App\Traits\HasCustomPagination;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasCustomPagination;
    
    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'is_calculated_by_experience',
        'default_days',
        'allows_carry_forward',
    ];

    /**
     *  Hides it from log outputs, array dumps, and JSON strings
     * 
     */
    protected $hidden = [
        'tenant_id',
    ];
}
