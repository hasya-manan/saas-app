<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;


class LeaveTypeController extends Controller
{
     public function index(Request $request): Response
    {
        $leaveTypes = LeaveType::where('tenant_id', auth()->user()->tenant_id)
                                ->with('tiers')
                                ->paginateDefault();

        return Inertia::render('CompanyAdmin/LeaveType/Index', [
            'leaveTypes' => $leaveTypes
        ]);
        
        //console.log(usePage().props);
    }

    //diff allowed_days and default_days :: if allowed_days exist 
    // then the data will be use that data instead of default_days


   public function update(Request $request, $id) 
{
    
    $validated = $request->validate([
        //levaes_type table validated
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:50',
        'is_calculated_by_experience' => 'boolean',
        'default_days' => 'sometimes|integer|min:0',
        'allows_carry_forward' => 'boolean',
        'probation_period_months' => 'sometimes|integer|min:0', 
        'is_active' => 'boolean',
         'is_pro_rata' => 'boolean',
        // tier table validated
        'min_years' => 'decimal|min:0',
        'max_years' => 'decimal|min:0',
        'allowed_days' => 'sometimes|integer|min:0',
        'max_carry_forward_days' => 'sometimes|integer|min:0',
        
    ]);

   
 DB::transaction(function () use ($request, $id, $validated) {
        $leaveType = LeaveType::findOrFail($id);

        // Update the main model using the already-validated data
       
        $leaveType->update(array_intersect_key($validated, array_flip([
            'name', 'code', 'default_days', 'is_calculated_by_experience', 
            'probation_period_months', 'is_active', 'is_pro_rata'
        ])));

        // Update the child table
        if ($request->hasAny(['allowed_days', 'max_carry_forward_days'])) {
            $leaveType->tiers()->updateOrCreate(
                ['leave_type_id' => $leaveType->id], 
                [
                    'allowed_days' => $validated['allowed_days'] ?? null,
                    'max_carry_forward_days' => $validated['max_carry_forward_days'] ?? null,
                ]
            );
        }
    });

    return redirect()->back()->with('success', 'Leave type updated successfully.');
}
}
