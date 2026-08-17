<?php

namespace App\Models;

use App\Traits\HasCustomPagination;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveBalance extends Model
{
    use HasCustomPagination, SoftDeletes;
    
    // Explicitly define the table name if it doesn't follow Laravel's plural convention
    protected $table = 'leave_balances';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'leave_type_id',
        'year',
        'allotted_days',
        'carried_forward',
        'taken_days',
    ];

    /**
     * A leave balance belongs to a specific leave type 
     * (e.g., links leave_type_id to the leave_types table)
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    /**
     * A leave balance belongs to a user (staff member)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}