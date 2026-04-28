<?php

namespace App\Services;

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StaffService
{
    public function createStaff(array $validated, string $tenantId): void
    {
       
        DB::transaction(function () use ($validated, $tenantId) {

            // ── 1. CREATE USER FIRST ──────────────────────────
            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => bcrypt($validated['password']),
                'role_id'   => $validated['role_id'],
                'tenant_id' => $tenantId,
            ]);

            // ── 2. HANDLE DEPARTMENT ──────────────────────────
            [$departmentId, $supervisorId] = $this->resolveDepartment($validated, $tenantId, $user);

            // ── 3. UPDATE USER WITH DEPARTMENT ────────────────
            $user->update([
                'department_id' => $departmentId,
                'supervisor_id' => $supervisorId,
            ]);

            // ── 4. CREATE PROFILE ─────────────────────────────
            $user->profile()->create([
                'ic_number'          => $validated['ic_number'],
                'user_gender'        => $validated['user_gender'] ?? null,
                'dob'                => $validated['dob'] ?? null,
                'phone'              => $validated['phone'] ?? null,
                'join_date'          => $validated['join_date'] ?? null,
                'address_line_1'     => $validated['address_line_1'] ?? null,
                'address_line_2'     => $validated['address_line_2'] ?? null,
                'city'               => $validated['city'] ?? null,
                'postcode'           => $validated['postcode'] ?? null,
                'state'              => $validated['state'] ?? null,
                'waris_name'         => $validated['waris_name'] ?? null,
                'waris_gender'       => $validated['waris_gender'] ?? null,
                'waris_relationship' => $validated['waris_relationship'] ?? null,
                'waris_ic'           => $validated['waris_ic'] ?? null,
                'waris_phone'        => $validated['waris_phone'] ?? null,
            ]);

            // ── 5. CREATE FINANCES ────────────────────────────
            $user->finance()->create([
                'basic_salary'      => $validated['basic_salary'] ?? 0,
                'bank_name'         => $validated['bank_name'] ?? null,
                'bank_account_no'   => $validated['bank_account_no'] ?? null,
                'epf_no'            => $validated['epf_no'] ?? null,
                'epf_rate_employee' => $validated['epf_rate_employee'] ?? 11.00,
                'epf_rate_employer' => $validated['epf_rate_employer'] ?? 13.00,
                'socso_no'          => $validated['socso_no'] ?? null,
                'socso_type'        => $validated['socso_type'] ?? null,
                'tax_no'            => $validated['tax_no'] ?? null,
                'eis_enabled'       => $validated['eis_enabled'] ?? true,
            ]);
        });
    }

    // ── PRIVATE HELPER ────────────────────────────────────────
    // Separated department logic into its own method
    // because it's complex enough to deserve its own space
    private function resolveDepartment(array $validated, string $tenantId, User $user): array
    {
        $departmentId = null;
        $supervisorId = null;

        if (!empty($validated['name_department'])) {
            // New department
            $department = Department::create([
                'tenant_id'   => $tenantId,
                'name'        => $validated['name_department'],
                'description' => $validated['description'] ?? null,
                'hod_id'      => !empty($validated['is_hod'])
                                    ? $user->id
                                    : ($validated['hod_id'] ?? null),
            ]);
            $departmentId = $department->id;

            if (empty($validated['is_hod']) && !empty($validated['hod_id'])) {
                $supervisorId = $validated['hod_id'];
            }

        } else {
            // Existing department
            $departmentId = $validated['department_id'] ?? null;

            if ($departmentId) {
                $department = Department::find($departmentId);

                if (!empty($validated['is_hod'])) {
                    if ($department->hod_id) {
                        Log::info("HOD replaced for department {$department->id}: old={$department->hod_id}, new={$user->id}");
                    }
                    $department->update(['hod_id' => $user->id]);
                } else {
                    $supervisorId = $department->hod_id ?? null;
                }
            }
        }

        return [$departmentId, $supervisorId];
    }
}