<?php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        //GOD MODE

        // If the user is NOT a SuperAdmin (Role 1), then filter by tenant
        if (auth()->check() && auth()->user()->role_id !== 1) {
            $builder->where('tenant_id', auth()->user()->tenant_id);
        }
    }
}