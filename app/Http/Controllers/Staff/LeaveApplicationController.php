<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\GlobalLookup;
use App\Models\LeaveApplication;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaveApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

      $user = auth()->user();

        return Inertia::render('Staff/ApplyLeave/index', [
            'leaveTypes' => LeaveType::where('tenant_id', $user->tenant_id)
                ->where('is_active', 1)
                ->get(),

            
            'leaveBalances' => LeaveBalance::where('tenant_id', $user->tenant_id)
                ->where('user_id', $user->id)
                ->where('year', date('Y'))
                ->get(),

            'lookups' => [
            'leave_duration' => GlobalLookup::where('category', 'leave_duration')
                ->orderBy('sort_order')
                ->get(),
        ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $request->validate([
        'leave_type_id' => 'required|exists:leave_types,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'leave_duration' => 'required|in:full,am,pm',
        'reason' => 'required|string|max:500',
    ]);

    // 1. Calculate total days
    $start = Carbon::parse($request->start_date);
    $end = Carbon::parse($request->end_date);
    
    $daysDifference = $start->diffInDays($end) + 1;

    // If it's a single day and they chose half day (AM or PM), total is 0.5
    // If it's multiple days, you can adjust logic or restrict half-days to single-day requests only
    $totalDays = $daysDifference;
    if ($request->leave_duration !== 'full') {
        if ($daysDifference > 1) {
            return back()->withErrors(['leave_duration' => 'Half day duration can only be applied for single-day leaves.']);
        }
        $totalDays = 0.5;
    }

    // 2. Check if user has enough balance
    $balance = LeaveBalance::where('tenant_id', $user->tenant_id)
        ->where('user_id', $user->id)
        ->where('leave_type_id', $request->leave_type_id)
        ->where('year', date('Y'))
        ->first();

    if (!$balance) {
        return back()->withErrors(['leave_type_id' => 'No leave balance found for this type.']);
    }

    $remainingDays = ($balance->allotted_days + $balance->carried_forward) - $balance->taken_days;

    if ($totalDays > $remainingDays) {
        return back()->withErrors(['leave_type_id' => 'Insufficient leave balance for this request.']);
    }

    // 3. Create the leave application using your existing columns
    LeaveApplication::create([
        'tenant_id' => $user->tenant_id,
        'user_id' => $user->id,
        'leave_type_id' => $request->leave_type_id,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'leave_duration' => $request->leave_duration,
        'total_days' => $totalDays,
        'reason' => $request->reason,
        'status' => 'pending', 
    ]);

    // 4. Increment taken days in leave balances
    $balance->increment('taken_days', $totalDays);

    return redirect()->back()->with('success', 'Leave application submitted successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
