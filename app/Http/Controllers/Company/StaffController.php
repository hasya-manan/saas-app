<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role; 
use Illuminate\Http\Request;
use Inertia\Inertia;

class StaffController extends Controller
{
    
    /**
     * Display the list of staff (List.vue)
     */
    public function index()
    {
        $employees = User::where('tenant_id', auth()->user()->tenant_id)
            ->with(['role', 'profile'])
            ->latest()
            ->paginate(10);

        return Inertia::render('CompanyAdmin/Staff/List', [
            'employees' => $employees,
            // RATIONALE: Required here because the 'Quick Edit' panel 
            // is a child component/element of the List view.
            'roles' => Role::whereIn('id', [2, 3])->get(['id', 'name']),
        ]);
    }
    // RATIONALE: We use Type-Hinting (User $user) so Laravel 
    // automatically finds the staff member by the ID provided in the URL.
    public function update(Request $request, User $user)
    {
        // 1. Validate the data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
        ]);

        // 2. Update the user
        // NOTE: Because of multi-tenancy, our Global Scope ensures 
        // we can only update users belonging to our tenant.
        $user->update($validated);

        // 3. Return back with a success message
        return back()->with('success', "{$user->name} has been updated successfully.");
    }

    /**
     * Show the form to add new staff (Create.vue)
     */
    public function create()
    {
        return Inertia::render('CompanyAdmin/Staff/Create', [
            // Pass roles so the Admin can choose (Staff, Manager, etc.)
            // Usually IDs 2 and 3 for Company Admin and Staff
            'roles' => Role::whereIn('id', [2, 3])->get()
        ]);
    }
}
