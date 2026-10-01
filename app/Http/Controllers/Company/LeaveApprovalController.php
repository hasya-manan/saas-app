<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\LeaveApplication;
use App\Models\LeaveBalance;
use Carbon\Carbon;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class LeaveApprovalController extends Controller
{
   public function index(Request $request)
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;

        // HR, or anyone who has direct reports
        abort_unless(
            $user->role_id === 2 || \App\Models\User::where('supervisor_id', $user->id)->exists(),
            403
        );

        $tab = $request->query('tab', 'pending');

        $statuses = match ($tab) {
            'approved' => ['approved'],
            'history'  => ['approved', 'rejected', 'withdrawn'],
            default    => ['pending'],
        };

        // Direct reports of the logged-in user
        $directApprovals = LeaveApplication::where('tenant_id', $tenantId)
            ->whereHas('user', function ($query) use ($user) {
                $query->where('supervisor_id', $user->id);
            })
            ->whereIn('status', $statuses)
            ->with(['user', 'leaveType'])
            ->latest()
            ->get();

        $companyApprovals = collect();

        if ($user->role_id === 2) { // HR: everyone else in the tenant
            $companyApprovals = LeaveApplication::where('tenant_id', $tenantId)
                ->where('user_id', '!=', $user->id) // hide HR's own leave (can't self-approve)
                ->whereHas('user', function ($query) use ($user) {
                    $query->where(function ($q) use ($user) {
                        $q->where('supervisor_id', '!=', $user->id)
                        ->orWhereNull('supervisor_id');
                    });
                })
                ->whereIn('status', $statuses)
                ->with(['user', 'leaveType'])
                ->latest()
                ->get();
        }

        return Inertia::render('CompanyAdmin/ApproveLeave/Index', [
            'directApprovals'  => $directApprovals,
            'companyApprovals' => $companyApprovals,
            'tab'              => $tab,
        ]);
    }

    public function updateStatus(Request $request, LeaveApplication $leave)
{
    $data = $request->validate([
        'status'  => 'required|in:approved,rejected,withdrawn',
        'remarks' => 'nullable|string|max:500',
    ]);

    if (! $this->canDecide($leave)) {
        return redirect()->back()->with('error', 'You are not allowed to decide this leave.');
    }

    // current status => statuses it may move to
    $allowed = [
        'pending'  => ['approved', 'rejected'],
        'approved' => ['withdrawn'],
    ];

    try {
        DB::transaction(function () use ($leave, $data, $allowed) {
            // Re-read inside the transaction so a double click can't process it twice
            $leave = LeaveApplication::whereKey($leave->id)->lockForUpdate()->firstOrFail();

            abort_unless(
                in_array($data['status'], $allowed[$leave->status] ?? [], true),
                422,
                'This change is not allowed for the current status.'
            );

            $leave->update([
                'status'      => $data['status'],
                'approved_by' => auth()->id(),
                'remarks'     => $data['remarks'] ?? null,
            ]);

            // Rejected or withdrawn leave no longer counts, so release the days
            if (in_array($data['status'], ['rejected', 'withdrawn'], true)) {
                $year = Carbon::parse($leave->start_date)->year;

                $balance = LeaveBalance::where('tenant_id', $leave->tenant_id)
                    ->where('user_id', $leave->user_id)
                    ->where('leave_type_id', $leave->leave_type_id)
                    ->where('year', $year)
                    ->lockForUpdate()
                    ->first();

                if ($balance) {
                    $totalTaken = LeaveApplication::where('tenant_id', $leave->tenant_id)
                        ->where('user_id', $leave->user_id)
                        ->where('leave_type_id', $leave->leave_type_id)
                        ->whereYear('start_date', $year)
                        ->whereIn('status', ['pending', 'approved'])
                        ->sum('total_days');

                    $balance->update(['taken_days' => $totalTaken]);
                }
            }
        });
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        return redirect()->back()->with('error', $e->getMessage());
    }

    return redirect()->back()->with('success', 'Leave ' . $data['status'] . '.');
}

    private function canDecide(LeaveApplication $leave): bool
    {
        $user = auth()->user();

        // Super admin does not decide tenant leave
        if ($user->role_id === 1) return false;

        // Tenant check (also covered by your TenantScope)
        if (! $user->tenant_id || $leave->tenant_id !== $user->tenant_id) return false;

        // No self-approval
        if ($leave->user_id === $user->id) return false;

        // HR can decide for anyone in the tenant
        if ($user->role_id === 2) return true;

        // Otherwise only the applicant's direct supervisor
        return $leave->user->supervisor_id === $user->id;
    }



}
