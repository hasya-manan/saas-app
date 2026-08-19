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

    $start = Carbon::parse($request->start_date);
    $end = Carbon::parse($request->end_date);
    
    $totalDays = 0;

    // 1. Loop through each day from start_date to end_date
    $current = $start->copy();
    while ($current->lte($end)) {
        // Check if the current day is NOT Saturday (6) and NOT Sunday (7)
        if (!$current->isWeekend()) {
            $totalDays += 1;
        }
        $current->addDay();
    }

    // If no valid working days were counted (e.g. they picked Sat-Sun)
    if ($totalDays === 0) {
        return back()->withErrors(['start_date' => 'Selected dates fall entirely on weekends.']);
    }

    // 2. Adjust for Half Day (AM/PM)
    if ($request->leave_duration !== 'full') {
        if ($totalDays > 1) {
            return back()->withErrors(['leave_duration' => 'Half-day duration can only be applied to a single working day.']);
        }
        $totalDays = 0.5;
    }

    // 3. Check leave balance
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

    // 4. Save application
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

    // 5. Update balance
    $balance->increment('taken_days', $totalDays);

    return redirect()->back()->with('success', 'Leave application submitted successfully!');
}

    /**
     * Display the specified resource.
     */
   public function show(string $id)
    {
        $user = auth()->user();

        // The GlobalScope automatically handles tenant isolation!
        $leaveApplication = LeaveApplication::where('user_id', $user->id)
            ->findOrFail($id);

        // return Inertia::render('Staff/ApplyLeave/show', [
        //     'leaveApplication' => $leaveApplication,
        // ]);
        return response()->json([
        'success' => true,
        'leaveApplication' => $leaveApplication,
    ]);
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
