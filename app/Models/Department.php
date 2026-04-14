<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    //
    protected $fillable = [
        'tenant_id', 
        'name', 
        'description', 
        'manager_id', 
        'join_date'
    ];

    /**
     * Get the Head of Department (HOD)
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get all staff belonging to this department
     */
    public function staff(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
