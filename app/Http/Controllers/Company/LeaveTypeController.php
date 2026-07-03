<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\LeaveTier;
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
        'tiers' => 'required|array', // Validate that tiers is an array
        'tiers.*.min_years' => 'required|numeric|min:0',
        'tiers.*.max_years' => 'required|numeric|min:0',
        'tiers.*.allowed_days' => 'required|integer|min:0',
        'tiers.*.max_carry_forward_days' => 'required|integer|min:0',
        'tiers.*.id' => 'sometimes|integer|exists:leave_type_tiers,id',
        
    ]);

   $leaveType = LeaveType::findOrFail($id); // Define this here
    DB::transaction(function () use ($request, $leaveType) {
        // 1. Update main model as usual
        $leaveType->update($request->only([ 'name', 'code', 'default_days', 'is_calculated_by_experience', 

                'probation_period_months', 'is_active', 'is_pro_rata']));

        // 2. Get the IDs of the tiers currently in the request
        $incomingTiers = collect($request->tiers);
        
        // 3. This acts as a "whitelist.", savedIds
        // By collecting the IDs of everything you just processed, you are telling Laravel: 
        // "These are the rows that should exist
        $savedIds = [];

        foreach ($incomingTiers as $tierData) {
            // Update if it has an ID, otherwise create a new one
            $tier = $leaveType->tiers()->updateOrCreate(
                ['id' => $tierData['id'] ?? null], // Match by ID if it exists
                [
                    'min_years' => $tierData['min_years'],
                    'max_years' => $tierData['max_years'],
                    'allowed_days' => $tierData['allowed_days'],
                    'max_carry_forward_days' => $tierData['max_carry_forward_days'],
                    'tenant_id' => $leaveType->tenant_id,
                ]
            );
            $savedIds[] = $tier->id;
        }

        // 4. IMPORTANT: Only delete the ones the user removed from the UI
     
        $leaveType->tiers()->whereNotIn('id', $savedIds)->delete();
    });

    return redirect()->back()->with('success', 'Leave type updated successfully.');
}

public function destroy(LeaveTier $tier)
{
    
    if ($tier->tenant_id !== auth()->user()->tenant_id) {
        abort(403);
    }

    $tier->delete();

    return back()->with('success', 'Tier removed successfully.');
}
}
