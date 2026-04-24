<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Role; 
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

/**
 * StaffController
 * 
 * Handles all staff management operations for the tenant.
 * All queries are scoped by tenant_id to ensure data isolation.
 * login as admin company 
 * @author Hasya
 */

class StaffController extends Controller
{
    /**
     * Display the paginated list of staff members for the tenant (List.vue)
     * 
     * Includes roles for the Quick Edit panel which is embedded in the List view.
     * All staff are scoped to the authenticated user's tenant_id.
     * PAGE : /companyAdmin/staff/list
     */
    public function index()
    {
        $employees = User::where('tenant_id', auth()->user()->tenant_id)
            ->with(['role', 'profile'])
            ->latest()
            ->paginate(10);

        return Inertia::render('CompanyAdmin/Staff/List', [
            'employees' => $employees,
             'roles'   => Role::where('id', '!=', 1)
                        ->select('id', 'name', 'display_name')
                        ->get(),
        ]);
    }
    /**
     * Update an existing staff member's details.
     * 
     * Uses route model binding (User $user) so Laravel automatically
     * resolves the staff member by ID from the URL.
     * 
     * Multi-tenancy is enforced via Global Scope — only users belonging
     * to the authenticated tenant can be updated.
     */    
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
        ]);
        $user->update($validated);
        return back()->with('success', "{$user->name} has been updated successfully.");
    }

    /**
     * Show the form to create a new staff member.
     * Passes roles (excluding superadmin) and tenant-scoped departments.
     * If no departments exist yet, the form will show a text input instead.
     * PAGE:/companyAdmin/staff/create
     */
    public function create()
    {
        $tenantId = auth()->user()->tenant_id; // ← missing this!

        return Inertia::render('CompanyAdmin/Staff/Create', [
            'roles' => Role::where('id', '!=', 1)
                        ->select('id', 'name', 'display_name')
                        ->get(),

            'departments' => Department::where('tenant_id', $tenantId)
                                ->orderBy('name')
                                ->get(['id', 'name']),
            'staffList'   => User::where('tenant_id', $tenantId)
                            ->select('id', 'name')
                            ->orderBy('name')
                            ->get()
        ]);
    }


//     store()
// ├── 1. Validate all fields
// ├── 2. If "others" → create new Department first → get its id
// ├── 3. Create User (with tenant_id + department_id)
// ├── 4. Create UserProfile (1:1)
// ├── 5. Create StaffFinance (1:1)
// └── 6. If is_hod → update department's hod_id to new user's id

// is_hod = true
// ├── new dept  → hod_id = $user->id, supervisor_id = null (they ARE the boss)
// └── exist dept → replace hod_id, supervisor_id = null

// is_hod = false (regular staff)
// ├── new dept  → hod_id = selected hod_id, supervisor_id = that selected hod
// └── exist dept → supervisor_id = department's current hod_id (auto-fetched)
    
    public function store(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            // users table
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|max:255|unique:users',
            'password'       => 'required|string|confirmed|min:8',
            'role_id'        => 'required|exists:roles,id',
            'department_id'  => 'nullable',

            // user_profiles table
            'ic_number'      => 'required|string|unique:user_profiles',
            'phone'          => 'nullable|string',
            'user_gender'    => 'nullable|string',
            'dob'            => 'nullable|date',
            'join_date'      => 'nullable|date',
            'address_line_1' => 'nullable|string',
            'address_line_2' => 'nullable|string',
            'city'           => 'nullable|string',
            'postcode'       => 'nullable|string',
            'state'          => 'nullable|string',
            'waris_name'         => 'nullable|string',
            'waris_gender'       => 'nullable|string',
            'waris_relationship' => 'nullable|string',
            'waris_ic'           => 'nullable|string',
            'waris_phone'        => 'nullable|string',

            // staff_finances table
            'basic_salary'      => 'nullable|numeric',
            'bank_name'         => 'nullable|string',
            'bank_account_no'   => 'nullable|string',
            'epf_no'            => 'nullable|string',
            'epf_rate_employee' => 'nullable|numeric',
            'epf_rate_employer' => 'nullable|numeric',
            'socso_no'          => 'nullable|string',
            'socso_type'        => 'nullable|string',
            'tax_no'            => 'nullable|string',
            'eis_enabled'       => 'nullable|boolean',

            //if have = new department fields
            'name_department' => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'hod_id'          => 'nullable|exists:users,id',
            'is_hod'          => 'nullable|boolean',
        ]);

        // If fails, ALL inserts are rolled back automatically
        DB::transaction(function () use ($validated, $tenantId) {

            // ── 1. CREATE USER FIRST ───────────────────────────────
            // User is created first (without department_id) because if
            // is_hod is true, we need $user->id to assign as hod_id
            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => bcrypt($validated['password']),
                'role_id'   => $validated['role_id'],
                'tenant_id' => $tenantId,
            ]);

            // ── 2. HANDLE DEPARTMENT ───────────────────────────────
            $departmentId = null;
            $supervisorId = null;

            if (!empty($validated['name_department'])) {
                // "Others" was selected → create brand new department
                // If is_hod is true, assign new user as HOD immediately
                $department = Department::create([
                    'tenant_id'   => $tenantId,
                    'name'        => $validated['name_department'],
                    'description' => $validated['description'] ?? null,
                    'hod_id'      => !empty($validated['is_hod']) 
                                        ? $user->id 
                                        : ($validated['hod_id'] ?? null),
                ]);
                $departmentId = $department->id;
                // If new staff is NOT the HOD, and someone was assigned as HOD,
                // that person becomes the supervisor
                if (empty($validated['is_hod']) && !empty($validated['hod_id'])) {
                    $supervisorId = $validated['hod_id'];
                }

            } else {
                // Existing department was selected from dropdown
                $departmentId = $validated['department_id'] ?? null;

                 if ($departmentId) {
                    $department = Department::find($departmentId);

                    if (!empty($validated['is_hod'])) {
                        // New staff is HOD — log replacement if dept already has one
                        if ($department->hod_id) {
                            Log::info("HOD replaced for department {$department->id}: old hod_id={$department->hod_id}, new hod_id={$user->id}");
                        }
                        $department->update(['hod_id' => $user->id]);

                    } else {
                        // Regular staff → supervisor = current HOD of the department
                        $supervisorId = $department->hod_id ?? null;
                    }
                }
            }

            // ── 3. UPDATE USER WITH DEPARTMENT ────────────────────
                    $user->update([
                        'department_id' => $departmentId,
                        'supervisor_id' => $supervisorId,
                    ]);
            // ── 4. CREATE PROFILE ──────────────────────────────────
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

            // ── 5. CREATE FINANCES ─────────────────────────────────
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

        return back()->with('success', "{$validated['name']} has been created successfully.");
    }

    public function show()
    {
        return Inertia::render('CompanyAdmin/Staff/View', [
            
            'roles'   => Role::where('id', '!=', 1)
                        ->select('id', 'name', 'display_name')
                        ->get(),
        ]);
    }
}
