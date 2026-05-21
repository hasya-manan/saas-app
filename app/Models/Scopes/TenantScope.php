<?php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        //TODO :: any cron job that may need to filter by tenant
        // 1. BACKGROUND / QUEUE CHECK
        // If a background job or webhook or cronjob
        // if (app()->bound('current_tenant_id')) {
        //     $builder->where('tenant_id', app('current_tenant_id'));
        //     return;
        // }

        //GOD MODE : If the user is NOT a SuperAdmin (Role 1), then filter by tenant
        if (!auth()->check() || auth()->user()->role_id !== 1) {
        
        // For everyone else (Regular Users and Guests), force-filter by their tenant_id
        $tenantId = auth()->check() ? auth()->user()->tenant_id : null;

        $builder->where('tenant_id', $tenantId);
    }
    }
}