<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\GlobalLookup;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
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
        ]);

        // Logic: Keep generating until ID is unique
       do {
        // Generate 3 random numbers (100-999) &  Generate 3 random letters only (a-z)
        $numbers = rand(100, 999);
        $letters = Str::lower(Str::random(3, 'abcdefghijklmnopqrstuvwxyz'));
        $customId = "t-{$numbers}{$letters}";
    
        } while (Tenant::where('id', $customId)->exists());

        // Create the Tenant record
       $tenant = Tenant::create([
        'id'           => $customId,
        'company_name' => $validated['company_name'],
        'email'        => $validated['admin_email'],
        'status'       => 'active', 
        ]);

        // TODO: if wants to have a domain for each tenant
        // $tenant->domains()->create([
        //     'domain' => $customId . '.localhost'
        // ]);

       
        User::create([
            'name'      => $validated['admin_name'],
            'email'     => $validated['admin_email'],
            'password'  => bcrypt('password123'), // TODO::maybe we can make their phone number as their password 
            'tenant_id' => $tenant->id,           // Link them to the new company
            'role_id'   => Role::where('name', 'admin_company')->value('id'), //  fetch from DB
                    
        ]);

        return redirect()->back()
        ->with('success', "Company $customId has been fully set up!");
               

    }

    // =========================================
    // this is for the page company list all tenant
    // ========================================= 
    
    public function list(Request $request)
{
    // 1. Grab both inputs from the request
    $search = $request->search;
    $status = $request->status; 

    // 2. Create a reusable query logic
    $applyFilters = function ($query) use ($search, $status) {
        // Text Search (Name or Email)
        $query->when($search, function ($q) use ($search) {
            $q->where(function ($inner) use ($search) {
                $inner->where('company_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            });
        });

        //  Dropdown Filter (Exact Match)
        $query->when($status, function ($q) use ($status) {
            $q->where('status', $status);
        });
    };

    $tenants = Tenant::query()
        ->tap($applyFilters)
        ->latest()
        ->paginate(10)
        ->withQueryString();

    $deletedTenants = Tenant::onlyTrashed()
        ->tap($applyFilters)
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return Inertia::render('SuperAdmin/Tenants/List', [
        'tenants' => $tenants,
        'deletedTenants' => $deletedTenants,
        'statusOptions' => GlobalLookup::where('category', 'tenant_status')
            ->orderBy('sort_order')
            ->get(['key', 'label']),
        
        //  CRITICAL: You MUST include 'status' here so the dropdown stays selected
        'filters' => $request->only(['search', 'status']) 
    ]);
}

    // 2. The Archive (Soft Delete): Moves company from "Active" to "Trash"
    public function destroy(Tenant $tenant)
    {
        // Because the Model uses SoftDeletes, this only sets the 'deleted_at' column
        $tenant->update(['status' => 'archived']); // Change status to archived
        $tenant->delete(); 
        return redirect()->route('tenants.list')
            ->with('success', 'Company archived to Trash.');
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

        // 2. Validate ONLY company fields
        $validated = $request->validate([
            'company_name'  => 'required|string|max:255',
            'company_email' => 'required|email', 
            'status'        => 'required|exists:global_lookups,key',
        ]);

        // 3. Update the tenant record
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
        $users = User::withoutGlobalScopes()
            ->with(['tenant', 'role'])
            ->whereNotNull('tenant_id')
        
        
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                });
            })

            // 3. Filter by Role 
            ->when($request->role, function ($query, $role) {
                $query->whereHas('role', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            })

        // 4.  Filter by Specific Company
            ->when($request->tenant_id, function ($query, $tenantId) {
                $query->where('tenant_id', $tenantId);
            })

            ->latest()
            ->paginate(10)
            ->withQueryString(); // 5.  Keeps your search filters active when clicking "Next Page"

        return Inertia::render('SuperAdmin/Users/List', [
            'users' => $users,
            // 6. Send these props so the GlobalFilter knows what the current values are
            'filters' => $request->only(['search', 'role', 'tenant_id']),
            'tenants' => Tenant::select('id', 'company_name')->get(),
            'roles' => Role::select('id', 'name')->get(),
        ]);
    }

    public function updateUser(Request $request, User $user)
    {
    // Security check for multi-tenancy
       if (auth()->user()->role_id !== 1 && $user->tenant_id !== auth()->user()->tenant_id) {
        abort(403, 'Unauthorized access.');
    }

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id'  => 'required|integer|exists:roles,id', 
        ]);

        $user->update($validated);

        return back()->with('success', 'User updated successfully.');
}
}