<?php

namespace App\Traits;

trait HasCustomPagination
{
    public function scopePaginateDefault($query)
    {
        return $query->paginate(config('pagination.tenants', 10))
                     ->withQueryString();
    }
}
