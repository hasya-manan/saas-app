<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\GlobalLookup;
use App\Models\LeaveType;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Str;
use Inertia\Inertia;

class TenantController extends Controller
{

    // =========================================
    // this is for the page create a new company
    // ========================================= 
    public function index()
    {
        return Inertia::render('SuperAdmin/Tenants/Create');
    }

   public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'admin_name'   => 'required|string|max:255',
            'admin_email'  => 'required|email|unique:users,email',
            'company_email' => 'required|email|unique:tenants,email', 
        ]);

        // 1. Start the Transaction
        return DB::transaction(function () use ($validated) {
            
        
            do {
                $numbers = rand(100, 999);
                $letters = Str::lower(Str::random(3, 'abcdefghijklmnopqrstuvwxyz'));
                $customId = "t-{$numbers}{$letters}";
            } while (Tenant::where('id', $customId)->exists());

            // 2. Create Tenant
            $tenant = Tenant::create([
                'id'           => $customId,
                'company_name' => $validated['company_name'],
                'email'        => $validated['company_email'],
                'status'       => 'active', 
            ]);

        // 3. Create Default Leave Types (Triggers clean private method below)
            $this->provisionDefaultLeaveTypes($tenant->id);

            // 4. Create User Admin Account
            User::create([
                'name'      => $validated['admin_name'],
                'email'     => $validated['admin_email'],
                'password'  => bcrypt('password123'),
                'tenant_id' => $tenant->id,
                'role_id'   => Role::where('name', 'admin_company')->firstOrFail()->id, 

            
            ]);

            // 5. If everything above finishes without an error, the DB "commits"
            return redirect()->back()
                ->with('success', "Company $customId has been fully set up!");
                
        }); // 6. If ANY line fails, the DB "rolls back" automatically
    }

    /**
 * Keeps the store method readable by handling leave creation down here.
 */
    private function provisionDefaultLeaveTypes(string $tenantId): void
    {
        $defaults = [
            [
                'name' => 'Annual Leave',
                'code' => 'AL',
                'default_days' => 13,
                'is_calculated_by_experience' => true,
                'allows_carry_forward' => true,
                'probation_period_months' => 3,
                'is_pro_rata' => true,
            ],
            [
                'name' => 'Emergency Leave',
                'code' => 'EL',
                'is_calculated_by_experience' => false,
                'default_days' => 3,
                'allows_carry_forward' => false,
                'probation_period_months' => 0,
                'is_pro_rata' => false,
            ],
            [
                'name' => 'Medical Leave',
                'code' => 'MC',
                'is_calculated_by_experience' => false,
                'default_days' => 14,
                'allows_carry_forward' => false,
                'probation_period_months' => 0,
                'is_pro_rata' => false,
            ],
        ];

        foreach ($defaults as $type) {
            LeaveType::create([
                'tenant_id'                   => $tenantId,
                'name'                        => $type['name'],
                'code'                        => $type['code'],
                'is_calculated_by_experience' => $type['is_calculated_by_experience'],
                'default_days'                => $type['default_days'] ?? null,
                'allows_carry_forward'        => $type['allows_carry_forward'],
            ]);
        }
    }
    // =========================================
    // this is for the page company list all tenant
    // ========================================= 
    

//Update :: change controller for single resposibility principle
    public function list(Request $request)
    {
        $filters = $request->only(['search', 'status']);

        return Inertia::render('SuperAdmin/Tenants/List', [
            'tenants' => Tenant::filter($filters)->latest()
            ->paginateDefault(),
            
            
            'deletedTenants' => Tenant::onlyTrashed()->filter($filters)->latest()->paginate(10)->withQueryString(),
            
            'statusOptions' => GlobalLookup::where('category', 'tenant_status')
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->get(['key', 'label']),
                
                
            'filters' => $filters 
        ]);
    }

    // 2. The Archive (Soft Delete): Moves company from "Active" to "Trash"
    public function destroy(Tenant $tenant)
    {
        // Because the Model uses SoftDeletes, this only sets the 'deleted_at' column
        $tenant->update(['status' => 'deactivated']); 
        $tenant->delete(); 
        return redirect()->route('tenants.list')
            ->with('success', 'Company deactivated and moved to Trash.');
    }

    // 3. The Restore: Clears 'deleted_at' so it becomes "Active" again
    public function restore($id)    
    {
        // We use withTrashed() because normally Laravel can't "see" deleted rows
        $tenant = Tenant::withTrashed()->findOrFail($id);
        $tenant->restore();
        $tenant->update(['status' => 'active']);
        return back()->with('success', "Company {$tenant->company_name} restored successfully!");
    }

    
    public function forceDelete($id)
    {
        // This physically removes the record from the list
        Tenant::withTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Company permanently removed.');
    }
    public function update(Request $request, $id)
    {
        // 1. Find the tenant (including trashed in case you're editing an archived one)
        $tenant = Tenant::withTrashed()->findOrFail($id);

       
        $validated = $request->validate([
            'company_name'  => 'required|string|max:255',
            'company_email' => 'required|email', 
            'status'        => 'required|exists:global_lookups,key',
        ]);

       
        // We map 'company_email' from the form to 'email' in the database
        $tenant->update([
            'company_name' => $validated['company_name'],
            'email'        => $validated['company_email'], 
            'status'       => $validated['status'],
        ]);

        // 4. Redirect back with success toast
        return redirect()->back()->with('success', 'Company information updated successfully.');
    }

    // =========================================
    // this is for the page user list all tenant
    // =========================================    
    
public function userList(Request $request) 
{
    $filters = $request->only(['search', 'role', 'tenant_id']);

    $users = User::withoutGlobalScopes()
        ->with(['tenant' => function ($query) {
            $query->withTrashed();
        }, 'role'])
        ->whereNotNull('tenant_id')
        // 1. Apply existing User filters (Name, Email, Role)
        ->filter($filters) 
        ->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($q) { // <--- Added this wrapper
                $q->orWhereHas('tenant', function ($sub) {
                    $sub->withTrashed()->filter(['search' => request('search')]);
                });
            });
        })
        ->latest()
        ->paginateDefault();

    return Inertia::render('SuperAdmin/Users/List', [
        'users'   => $users,
        'filters' => $filters,
        'tenants' => Tenant::select('id', 'company_name')->get(),
        // RATIONALE: We exclude Role ID 1 because SuperAdmins shouldn't 
        // be reassigned or filtered within the Tenant User list.
        'roles'   => Role::where('id', '!=', 1)
                        ->select('id', 'name', 'display_name')
                        ->get(),
    ]);
}

 public function updateUser(Request $request, $id)
{
    $user = User::withoutGlobalScopes()->findOrFail($id);

    $validated = $request->validate([
        'name'    => 'required|string|max:255',
        'email'   => 'required|email|unique:users,email,' . $user->id,
        'role_id' => 'required|integer|exists:roles,id',
        
    ]);

    $user->fill($validated);
    $user->role_id = $validated['role_id'];

    // DO NOT set $user->tenant_id = $request->tenant_id here.
    // By simply not touching the tenant_id field, Laravel 
    // keeps the existing value in the database.

    $user->save();

    return redirect()->route('users.list')->with('success', 'User updated successfully.');
}
}