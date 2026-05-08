<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class TenantExists implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = auth()->user();
        $query = DB::table($this->table)->where('id', $value);

        // If NOT SuperAdmin, add the tenant filter
        if ($user->role_id !== 1) {
            $query->where('tenant_id', $user->tenant_id);
        }

        $exists = $query->exists();

        if (!$exists) {
            $fail("The selected {$attribute} is invalid.");
        }
    }
}
