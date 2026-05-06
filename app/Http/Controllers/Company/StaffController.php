<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Role; 
use App\Models\User;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Services\StaffService;
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


//     store() inside StaffService.php
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
    
   // ── Inject the service ──────────────────────────
    public function __construct(private StaffService $staffService) {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            // users table
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|max:255|unique:users',
            'password'       => 'required|string|confirmed|min:8',
            'role_id'        => 'required|exists:roles,id',
            'department_id'  => 'nullable',

            // user_profiles table
            'ic_number'      => 'required|string|unique:user_profiles',
            'position'       => 'nullable|string',
            'phone'          => 'nullable|string',
            'user_gender'    => 'nullable|string',
            'dob'            => 'nullable|date',
            'marital_status' => 'nullable|string',
            'join_date'      => 'nullable|date',
            'address_line_1' => 'nullable|string',
            'address_line_2' => 'nullable|string',
            'city'           => 'nullable|string',
            'postcode'       => 'nullable|string',
            'state'          => 'nullable|string',
            'waris_name'         => 'nullable|string',
            'waris_gender'       => 'nullable|string',
            'waris_relationship' => 'nullable|string',
            'waris_relationship_other' => 'nullable|string|max:50',
            'waris_ic'           => 'nullable|string',
            'waris_phone'        => 'nullable|string',

            // staff_finances table
            'basic_salary'      => 'required|numeric|min:0', // Required for payroll logic
            'bank_name'         => 'nullable|string|max:255',
            'bank_account_no'   => 'nullable|string|max:50',
            'epf_no'            => 'nullable|string|max:50',
            'epf_rate_employee' => 'required|numeric|between:0,100', // Added safety range
            'epf_rate_employer' => 'required|numeric|between:0,100', // Added safety range
            'socso_no'          => 'nullable|string|max:50',
            'socso_type'        => 'required|string', 
            'tax_no'            => 'nullable|string|max:50',
            'eis_enabled'       => 'required|boolean',

            // new department fields
            'name_department' => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'hod_id'          => 'nullable|exists:users,id',
            'is_hod'          => 'nullable|boolean',
        ]);

       
        // ── This one line replaces ALL the DB logic ──
        $this->staffService->createStaff($validated, auth()->user()->tenant_id);

        return back()->with('success', "{$validated['name']} has been created successfully.");
    }

        public function show(User $user) // Laravel finds the user by UUID automatically
    {
        // Load the relationships needed for your profile cards
        $user->load(['role', 'profile', 'supervisor']);

        return Inertia::render('CompanyAdmin/Staff/View', [
            'user'    => $user,
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
    // 1. Safety check for the profile ID if user do not have to others table
        $profileId = $user->profile?->id ?? 'NULL';

        $validated = $request->validate([
            // user table
            'name' => 'sometimes|required|string|max:255',
            'role_id' => 'sometimes|required|exists:roles,id',
            'department_id' => 'sometimes|required|exists:departments,id',

            // user_profiles table 
            'position' => 'sometimes|nullable|string',
            'user_gender' => 'sometimes|nullable|string',
            'phone' => 'sometimes|required|nullable|string',
            'dob' => 'sometimes|nullable|date',
            'marital_status' => 'sometimes|nullable|string',
            'join_date' => 'sometimes|required|date',
            'address_line_1' => 'sometimes|nullable|string',
            'address_line_2' => 'sometimes|nullable|string',
            'city' => 'sometimes|nullable|string',
            'postcode' => 'sometimes|nullable|string',
            'state' => 'sometimes|nullable|string',
            'waris_name' => 'sometimes|nullable|string',
            'waris_gender' => 'sometimes|nullable|string',
            'waris_relationship' => 'sometimes|nullable|string',
            //'waris_relationship_other' => 'sometimes|string|max:50',
            'waris_ic' => 'sometimes|nullable|string',
            'waris_phone' => 'sometimes|nullable|string',
            
            // The Fix: unique check while ignoring the current user's profile
            'ic_number' => [
                'sometimes',
                'required',
                'string',
                "unique:user_profiles,ic_number,{$profileId}" 
            ],
        ]);

        // 2. Use the service you declared in the constructor
        $this->staffService->updateStaff($user, $validated);

        // 3. Return back to refresh the data in the UI
        return back()->with('success', "{$user->name} has been updated successfully.");
    }
}
