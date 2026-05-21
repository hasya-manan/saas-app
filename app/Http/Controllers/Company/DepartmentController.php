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
    public function index(Request $request): Response
    {
       $filters = $request->only(['search', 'department_id']);

    return Inertia::render('CompanyAdmin/Department/Index', [
        'departments' => Department::forCurrentTenant()
            ->with('hod:id,name') 
            ->filter($filters) 
            ->latest()
            ->paginateDefault(),// use the HasCustomPagination trait
            

        'allDepartments' => Department::forCurrentTenant()
            ->select('id', 'name')
            ->get(),

        'users' => User::where('tenant_id', auth()->user()->tenant_id)
            ->select('id', 'name')
            ->get(),
            
        'filters' => $filters,
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
