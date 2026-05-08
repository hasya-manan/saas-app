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
        return Inertia::render('CompanyAdmin/Department/Index', [
            // Fetch departments only for this tenant, including the manager's name
            'departments' => Department::where('tenant_id', auth()->user()->tenant_id)
                ->with('hod:id,name') 
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
    //create a new department
   public function store(Request $request) 
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hod_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            // ... other fields
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;

        // 1. Create the Department
        $department = Department::create($validated);

        // 2. Update the User (Optional: only if you want the HOD to be IN the dept)
        if ($request->hod_id) {
            User::where('id', $request->hod_id)->update([
                'department_id' => $department->id
            ]);
        }

        return redirect()->back()->with('success', 'Department and HOD updated!');
    }

    public function update(Request $request, Department $department) 
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hod_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        // Update the department with new data
        $department->update($validated);

        // Update the User/HOD if needed
        if ($request->hod_id) {
            User::where('id', $request->hod_id)->update([
                'department_id' => $department->id
            ]);
        }

        return redirect()->back()->with('success', 'Department updated!');
    }
    
}
