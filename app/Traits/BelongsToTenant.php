<?php
namespace App\Traits;

use App\Models\Scopes\TenantScope;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        // // 1. Automatically apply the "WHERE tenant_id = ..." filter
        // static::addGlobalScope(new TenantScope);

        // // 2. Automatically SET the tenant_id when creating new data
        // static::creating(function ($model) {
        //     if (auth()->check() && auth()->user()->tenant_id !== null) {
        //         $model->tenant_id = auth()->user()->tenant_id;
        //     }
        // });

        // 1. Automatically apply the "WHERE tenant_id = ..." filter
        static::addGlobalScope(new TenantScope);

        // 2. Automatically SET the tenant_id when creating new data
        static::creating(function ($model) {
            // If the model already has a tenant_id set manually, keep it
            if ($model->tenant_id !== null) {
                return;
            }

            // A. Check if running via background job/webhook with a global container context
            if (app()->bound('current_tenant_id')) {
                $model->tenant_id = app('current_tenant_id');
                return;
            }

            // B. Fallback to your standard authenticated frontend user
            if (auth()->check() && auth()->user()->tenant_id !== null) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }
}