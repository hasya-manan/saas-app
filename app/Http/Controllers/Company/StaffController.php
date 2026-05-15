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
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'role', 'department_id']);
        $employees = User::where('tenant_id', auth()->user()->tenant_id)
        ->with(['role', 'profile'])
        // 2. Apply the filters to the query
        ->filter($filters) 
        ->latest()
        ->paginate(10)
        // 3. Keep the filters in the pagination links (important!)
        ->withQueryString();

        return Inertia::render('CompanyAdmin/Staff/List', [
            'employees' => $employees,
            'filters'   => $filters, // Pass back to Vue to keep the inputs filled
            'roles'     => Role::where('id', '!=', 1)
                            ->select('id', 'name', 'display_name')
                            ->get(),
            // 4. Added this so your Department Dropdown actually has options
            'departments' => Department::forCurrentTenant()
                            ->select('id', 'name')
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
        $tenantId = auth()->user()->tenant_id; 

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
        $tenantId = auth()->user()->tenant_id; 

        // Load the relationships needed for your profile cards
        $user->load(['role', 'profile', 'supervisor', 'department', 'finance']);

        return Inertia::render('CompanyAdmin/Staff/View', [
            'user'    => $user,
            'roles'   => Role::where('id', '!=', 1)
                            ->select('id', 'name', 'display_name')
                            ->get(),
          'departments' => Department::where('tenant_id', $tenantId)
                            ->with(['hod' => function($query) {
                                // We MUST select the primary key (id) AND the uuid for Vue
                                $query->select('id', 'uuid', 'name'); 
                            }]) 
                            ->orderBy('name')
                            ->get(['id', 'name', 'description', 'hod_id']), // Added hod_id here
            'staffList'   => User::where('tenant_id', $tenantId)
                            ->select('id', 'name', 'uuid', 'department_id')
                            ->orderBy('name')
                            ->get()
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
    // 1. Safety check for the profile ID if user do not have the others table
        $profileId = $user->profile?->id ?? 'NULL';
        //dd($request->only('waris_ic'));
        $validated = $request->validate([
            // user table
            'name' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'required|exists:departments,id',

            // user_profiles table 
            'position' => 'nullable|string',
            'user_gender' => 'nullable|string',
            'phone' => 'required|nullable|string',
            'dob' => 'nullable|date',
            'marital_status' => 'nullable|string',
            'join_date' => 'required|date',
            'address_line_1' => 'nullable|string',
            'address_line_2' => 'nullable|string',
            'city' => 'nullable|string',
            'postcode' => 'nullable|string',
            'state' => 'nullable|string',
            'waris_name' => 'nullable|string',
            'waris_gender' => 'nullable|string',
            'waris_relationship' => 'nullable|string',
            //'waris_relationship_other' => 'string|max:50',
            'waris_ic' => 'nullable|string',
            'waris_phone' => 'nullable|string',
            
            // unique check while ignoring the current user's profile
            'ic_number' => [
                
                'required',
                'string',
                "unique:user_profiles,ic_number,{$profileId}" 
            ],

            //table department 
         
            'department_id'  => 'required', // Removed exists:departments,id because it might be 'others'
            'name_department'=> 'required_if:department_id,others|nullable|string|max:255',
            'description'    => 'nullable|string',
            'hod_id'         => 'nullable|exists:users,id',
            'is_hod'         => 'boolean',

            //table finance
            'basic_salary'      => 'nullable|numeric|min:0',
            'bank_name'         => 'nullable|string|max:255',
            'bank_account_no'   => 'nullable|string|max:50',
            'epf_no'            => 'nullable|string|max:50',
            
           
            'epf_rate_employee' => 'required|numeric|between:0,100',
            'epf_rate_employer' => 'required|numeric|between:0,100',
            
            'socso_no'          => 'nullable|string|max:50',
            'socso_type'        => 'required|string', 
            'tax_no'            => 'nullable|string|max:50',
            
            
            'eis_enabled'       => 'boolean',
        ]);

        // 2. Use the service you declared in the constructor
        $this->staffService->updateStaff($user, $validated);

        // 3. Return back to refresh the data in the UI
        return back()->with('success', "{$user->name} has been updated successfully.");
    }
}
