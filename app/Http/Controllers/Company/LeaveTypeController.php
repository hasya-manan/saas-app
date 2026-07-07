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
        'tiers' => 'nullable|array',
        'tiers.*.min_years' => 'required_with:tiers|numeric|min:0',
        'tiers.*.max_years' => 'required_with:tiers|numeric|min:0',
        'tiers.*.allowed_days' => 'required_with:tiers|integer|min:0',
        'tiers.*.max_carry_forward_days' => 'required_with:tiers|integer|min:0',
        'tiers.*.id' => 'sometimes|integer|exists:leave_tiers,id'
        
    ]);

   $leaveType = LeaveType::findOrFail($id); 
    DB::transaction(function () use ($request, $leaveType, $validated) {
        // 1. Update using validated because if data has ( leaves AND tier ) OR ( leaves only )
        $leaveType->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'default_days' => $validated['default_days'] ?? 0,
            'is_calculated_by_experience' => $validated['is_calculated_by_experience'] ?? false,
            'allows_carry_forward' => $validated['allows_carry_forward'] ?? false,
            'probation_period_months' => $validated['probation_period_months'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
            'is_pro_rata' => $validated['is_pro_rata'] ?? false,
        ]);

        // 2. Get the IDs of the tiers currently in the request
        $incomingTiers = collect($request->tiers);
        
        // 3. This acts as a "whitelist.", savedIds
        // By collecting the IDs of everything you just processed, you are telling Laravel: 
        // "These are the rows that should exist
        $savedIds = [];

        foreach ($incomingTiers as $tierData) {
            // Update if it has an ID, otherwise create a new one
            $tier = $leaveType->tiers()->updateOrCreate(
                ['id' => $tierData['id'] ?? null], 
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



public function destroyLeaveType(LeaveType $leavetype)
{
    // Log the deletion attempt
    \Log::info('LeaveType Deletion Attempt', [
        'user_id' => auth()->id(),
        'tenant_id' => auth()->user()->tenant_id,
        'leave_type_id' => $leavetype->id,
        'leave_type_name' => $leavetype->name
    ]);

    // Security check
    if ($leavetype->tenant_id !== auth()->user()->tenant_id) {
        \Log::warning('Unauthorized Delete Attempt', [
            'user_id' => auth()->id(),
            'attempted_leave_type_id' => $leavetype->id
        ]);
        abort(403, 'Unauthorized access.');
    }

    $leavetype->delete();
    return back()->with('success', 'Leave type removed successfully.');
}
public function store (Request $request) 
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:50',
        'is_calculated_by_experience' => 'boolean',
        'default_days' => 'sometimes|integer|min:0',
        'allows_carry_forward' => 'boolean',
        'probation_period_months' => 'sometimes|integer|min:0', 
        'is_active' => 'boolean',
         'is_pro_rata' => 'boolean',
        // tier table validated
        'tiers' => 'nullable|array',
        'tiers.*.min_years' => 'required_with:tiers|numeric|min:0',
        'tiers.*.max_years' => 'required_with:tiers|numeric|min:0',
        'tiers.*.allowed_days' => 'required_with:tiers|integer|min:0',
        'tiers.*.max_carry_forward_days' => 'required_with:tiers|integer|min:0',
    ]);

    $leaveType = LeaveType::create([
        'name'      => $validated['name'],
        'tenant_id' => $request->user()->tenant_id,
        'code' => $validated['code'],
        'is_calculated_by_experience' => $validated['is_calculated_by_experience'],
        'default_days' => $validated['default_days'],
        'allows_carry_forward' => $validated['allows_carry_forward'],
        'probation_period_months' => $validated['probation_period_months'],
        'is_active' => $validated['is_active'],
        'is_pro_rata' => $validated['is_pro_rata'],
      
    ]);
    if (!empty($validated['tiers'])) {
        foreach ($validated['tiers'] as $tier) {
            $leaveType->tiers()->create([
                'tenant_id' => $request->user()->tenant_id,
                'min_years' => $tier['min_years'],
                'max_years' => $tier['max_years'],
                'allowed_days' => $tier['allowed_days'],
                'max_carry_forward_days' => $tier['max_carry_forward_days'],
               
            ]);
        }
    }

    return redirect()->back()->with('success', 'Leave Type created successfully.');

}
}
