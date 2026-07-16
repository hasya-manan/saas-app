<?php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
  public function apply(Builder $builder, Model $model): void
    {
        // 1. Check if a user is logged in
        $user = auth()->user();

        // 2. Bypass Scope if user is SuperAdmin (Role 1)
        // This is your 'God Mode' bypass. It ignores tenant_id completely for role 1.
        if ($user && $user->role_id === 1) {
            return;
        }

        // 3. For all other users, ensure they have a valid tenant_id string.
        // If they are missing a tenant_id, this triggers the 'Safety Switch'.
        if (!$user || empty($user->tenant_id)) {
            $builder->whereRaw('1 = 0');
            return;
        }

        // 4. Filter by their specific tenant identifier (e.g., 't-3822u1')
        $builder->where('tenant_id', $user->tenant_id);
    }
}