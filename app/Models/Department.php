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

    public function scopeForCurrentTenant($query)
{
    $user = auth()->user();

    // If the user is a Superadmin, return the query without tenant restrictions
    if ($user->hasRole('superadmin')) { 
        return $query;
    }

    // Otherwise, restrict to their specific tenant
    return $query->where('tenant_id', $user->tenant_id);
}
    // app/Models/Department.php

    // app/Models/Department.php

public function scopeFilter($query, array $filters)
{
    $query->when($filters['search'] ?? null, function ($q, $search) {
        $q->where(function ($inner) use ($search) {
            // Check the Department's own name
            $inner->where('name', 'like', "%{$search}%")
            
            // AND check the HOD's name (reach into the User model)
            ->orWhereHas('hod', function ($hodQuery) use ($search) {
                $hodQuery->where('name', 'like', "%{$search}%");
            });
        });
    });

    // Handle the dropdown selection
    $query->when($filters['department_id'] ?? null, function ($q, $id) {
        $q->where('id', $id);
    });
}
    
}
