<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    //
    protected $fillable = [
        'tenant_id', 
        'name', 
        'description', 
        'hod_id', 
        'join_date'
    ];

    /**
     * Get the Head of Department (HOD)
     */
    public function hod() {
     return $this->belongsTo(User::class, 'hod_id');
    }
    /**
     * Get all staff belonging to this department
     */
    public function staff(): HasMany
    {
        return $this->hasMany(User::class);
    }

    
}
