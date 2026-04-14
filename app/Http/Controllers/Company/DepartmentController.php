<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;


class DepartmentController extends Controller
{

    /**
 * Display the department listing page.
 */
    public function index(): Response
    {
        return Inertia::render('CompanyAdmin/Departments/Index', [
            // Fetch departments only for this tenant, including the manager's name
            'departments' => Department::where('tenant_id', auth()->user()->tenant_id)
                ->with('manager:id,name') 
                ->latest()
                ->get(),

            // Fetch users for this tenant to populate the "Assign HOD" dropdown
            'users' => User::where('tenant_id', auth()->user()->tenant_id)
                ->select('id', 'name')
                ->get(),
            
            // Flash messages (optional, if not handled globally)
            'status' => session('success'),
        ]);
    }
    //
    public function create(Request $request) 
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id', // The HOD
            'join_date' => 'nullable|date', // As per your requirement
        ]);

        // Automatically inject the current tenant_id
        $validated['tenant_id'] = auth()->user()->tenant_id;

        Department::create($validated);

        return redirect()->back()->with('success', 'Department created successfully!');
    }
}
