<?php
namespace App\Traits;

use App\Models\Scopes\TenantScope;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        // 1. Automatically apply the "WHERE tenant_id = ..." filter
        static::addGlobalScope(new TenantScope);

        // 2. Automatically SET the tenant_id when creating new data
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->tenant_id !== null) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }
}