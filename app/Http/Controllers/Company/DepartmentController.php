<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
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
