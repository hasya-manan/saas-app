<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\LeaveApplication;
use Inertia\Inertia;
use Illuminate\Http\Request;



class LeaveApprovalController extends Controller
{
   public function index(Request $request)
{
    $user = auth()->user();
    $tenantId = $user->tenant_id;

    if ($user->role_id === 2) { // Company Admin
        // Priority 1: Leave applications where the applicant's supervisor_id in users table equals the Admin's ID
        $directApprovals = LeaveApplication::where('tenant_id', $tenantId)
            ->whereHas('user', function ($query) use ($user) {
                $query->where('supervisor_id', $user->id);
            })
            ->where('status', 'pending')
            ->with('user') // Eager load user details for the table
            ->get();

        // Priority 2: All other pending leave requests in the tenant (where applicant's supervisor is NOT this admin)
        $companyApprovals = LeaveApplication::where('tenant_id', $tenantId)
            ->whereHas('user', function ($query) use ($user) {
                $query->where('supervisor_id', '!=', $user->id)
                      ->orWhereNull('supervisor_id'); // Optional: catch users with no supervisor assigned yet
            })
            ->where('status', 'pending')
            ->with('user')
            ->get();

    } else { // Regular Supervisor (Role ID 3)
        // Direct reports for this specific supervisor
        $directApprovals = LeaveApplication::where('tenant_id', $tenantId)
            ->whereHas('user', function ($query) use ($user) {
                $query->where('supervisor_id', $user->id);
            })
            ->where('status', 'pending')
            ->with('user')
            ->get();

        $companyApprovals = collect(); // Empty for normal supervisors
    }

    return Inertia::render('CompanyAdmin/ApproveLeave/Index', [
        'directApprovals' => $directApprovals,
        'companyApprovals' => $companyApprovals,
    ]);
}
}
