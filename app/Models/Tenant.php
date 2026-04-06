<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains, SoftDeletes;

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'company_name', 
            'status',
            'email',
            'plan',
            'deleted_at',
        ];
    }

    protected static function booted()
    {
    static::deleting(function ($tenant) {
        // We use withoutGlobalScopes() so SuperAdmin isn't blocked by Tenancy
        $relation = $tenant->users()->withoutGlobalScopes();

        if ($tenant->isForceDeleting()) {
            $relation->withTrashed()->forceDelete();
        } else {
            $relation->delete();
        }
    });

    // Use restored (with a 'd') to make sure the Tenant record clears first
    static::restored(function ($tenant) {
        // Bring back users regardless of tenancy scope
        $tenant->users()->withoutGlobalScopes()->withTrashed()->restore();
    });
    }

    public function users() {
        return $this->hasMany(User::class);
    }

    public function adminUser() {
    return $this->hasOne(User::class, 'tenant_id');
    }

    // filtering 
    
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($inner) use ($search) {
                $inner->where('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        })->when($filters['status'] ?? null, function ($q, $status) {
            $q->where('status', $status);
        });
    }
}
