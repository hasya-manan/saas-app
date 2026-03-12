<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TenantController extends Controller
{
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

        return redirect()->route('superadmin.dashboard')
            ->with('message', "Company $customId and Admin created!");
    }

    // 1. The Main List: Shows both Active and Trashed companies to the UI
    public function list()
    {
        return Inertia::render('SuperAdmin/Tenants/List', [
            'tenants' => Tenant::all(), 
            'deletedTenants' => Tenant::onlyTrashed()->get(),
        ]);
    }

    // 2. The Archive (Soft Delete): Moves company from "Active" to "Trash"
    public function destroy(Tenant $tenant)
    {
        // Because the Model uses SoftDeletes, this only sets the 'deleted_at' column
        $tenant->delete(); 
        return redirect()->route('tenants.list')
            ->with('message', 'Company archived to Trash.');
    }

    // 3. The Restore: Clears 'deleted_at' so it becomes "Active" again
    public function restore($id)    
    {
        // We use withTrashed() because normally Laravel can't "see" deleted rows
        Tenant::withTrashed()->findOrFail($id)->restore();
        return back()->with('message', 'Company restored successfully!');
    }

    // 4. The Nuclear Option (Hard Delete): Erases the row from the database forever
    public function forceDelete($id)
    {
        // This physically removes the record from the disk
        Tenant::withTrashed()->findOrFail($id)->forceDelete();

        return back()->with('message', 'Company permanently removed.');
    }
    public function update(Request $request, $id)
    {
    
        $tenant = Tenant::withTrashed()->findOrFail($id);

    
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:tenants,email,' . $id,
        ]);

    
        $tenant->update($validated);

        // 4. Redirect back with a success flash message
        return redirect()->back()->with('success', 'Company updated successfully.');
}
}