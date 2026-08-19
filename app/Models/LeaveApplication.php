<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApplication extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];
    protected $fillable = [
        'user_id',
        'tenant_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'leave_duration',
        'total_days',
        'reason',
        'status',
        'approved_by',
        'remarks',
    ];
}
