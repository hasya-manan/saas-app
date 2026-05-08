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

             // PREPARE DATA: Clean up the UI-specific "other" logic
            // ── 4. RESOLVE RELATIONSHIP LOGIC ─────────────────
            // If 'other' is selected, use the custom text. Otherwise use dropdown value.
            $finalRelationship = ($validated['waris_relationship'] === 'other') 
                ? ($validated['waris_relationship_other'] ?: 'Other') 
                : $validated['waris_relationship'];
            // Force everything to lowercase and replace spaces with underscores
            // This makes "Step Father" become "step_father"
            $finalRelationship = strtolower(str_replace(' ', '_', trim($rawRelationship)));

            // ── 4. CREATE PROFILE ─────────────────────────────
            $user->profile()->create([
                'ic_number'          => $validated['ic_number'],
                'user_gender'        => $validated['user_gender'] ?? null,
                'dob'                => $validated['dob'] ?? null,
                'marital_status'     => $validated['marital_status'] ?? null,
                'phone'              => $validated['phone'] ?? null,
                'position'           => $validated['position'] ?? null,
                'join_date'          => $validated['join_date'] ?? null,
                'address_line_1'     => $validated['address_line_1'] ?? null,
                'address_line_2'     => $validated['address_line_2'] ?? null,
                'city'               => $validated['city'] ?? null,
                'postcode'           => $validated['postcode'] ?? null,
                'state'              => $validated['state'] ?? null,
                'waris_name'         => $validated['waris_name'] ?? null,
                'waris_gender'       => $validated['waris_gender'] ?? null,
                'waris_relationship' => $validated['finalRelationship'] ?? null,
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

    /**
     * Handle staff updates (Independent segments)
     */
  public function updateStaff(User $user, array $data)
{
    return DB::transaction(function () use ($user, $data) {
        $currentUser = auth()->user();

        // --- THE FIX FOR NULL TENANT_ID ---
        // If I am a SuperAdmin, I use the company ID of the user I am editing.
        // If I am a Company Admin, I use my own company ID.
        $tenantId = ($currentUser->role_id === 1) 
            ? $user->tenant_id 
            : $currentUser->tenant_id;

        // ---  CLEANUP STEP ---
        // If they are being assigned a department, clear their HOD status from ALL departments first
        // This prevents them from being HOD in two places at once.
        if (isset($data['department_id'])) {
            Department::where('tenant_id', $tenantId)
                ->where('hod_id', $user->id)
                ->update(['hod_id' => null]);
        }

        // 1. Handle Department Creation/Selection
        if (isset($data['department_id'])) {
            if ($data['department_id'] === 'others') {
                // Priority: 1. Specific hod_id from dropdown, 2. Current user if is_hod is checked
                $finalHodId = $data['hod_id'] ?? (($data['is_hod'] ?? false) ? $user->id : null);

                // Create new department for the tenant
                $department = Department::create([
                    'tenant_id'   => $tenantId,
                    'name'        => $data['name_department'] ?? 'New Department',
                    'description' => $data['description'] ?? null,
                    'hod_id'      => $finalHodId,
                ]);

                // CRITICAL: Overwrite 'others' with the new real ID so $user->update() works
                $data['department_id'] = $department->id;
                
            } else {
                // Update HOD status for an existing department
                if ($data['is_hod'] ?? false) {
                    Department::where('id', $data['department_id'])
                        ->where('tenant_id', $tenantId)
                        ->update(['hod_id' => $user->id]);
                } else {
                    // Optional: Remove HOD status if they were the HOD but are no longer
                    Department::where('id', $data['department_id'])
                        ->where('tenant_id', $tenantId)
                        ->where('hod_id', $user->id)
                        ->update(['hod_id' => null]);
                }
            }
        }

        // 3. ROLE ASSIGNMENT (Manual because not fillable) security purposes
        if (isset($data['role_id'])) {
            $newRole = (int)$data['role_id'];
            
            // Only SuperAdmin can assign Role 1
            if ($newRole === 1 && $currentUser->role_id !== 1) {
                abort(403, 'Unauthorized.');
            }

            if ($currentUser->role_id === 1 || in_array($newRole, [2, 3])) {
                $user->role_id = $newRole;
            }
        }

        // 4. Update User Table (This links the user to the new or existing department_id)
        $user->update(array_intersect_key($data, array_flip([
            'name', 
            'role_id', 
            'department_id'
        ])));

        // 5. Update Profile Table
        $profileFields = [
            'ic_number', 'user_gender', 'phone', 'position', 
            'dob', 'marital_status', 'join_date', 'address_line_1', 'address_line_2', 
            'city', 'postcode', 'state', 'waris_name', 'waris_gender', 'waris_relationship', 
            'waris_ic', 'waris_phone'
        ];
        $profileData = array_intersect_key($data, array_flip($profileFields));

        // 4. Update or Create Profile (Fixes "No Department/No Profile" issues)
        if (!empty($profileData)) {
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        }

        return $user->load(['profile', 'department', 'role']);
    });
}
}