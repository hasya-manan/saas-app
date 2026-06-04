<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;


class LeaveTypeController extends Controller
{
     public function index(Request $request): Response
    {
        $leaveTypes = LeaveType::where('tenant_id', auth()->user()->tenant_id)
                                ->paginateDefault();

        return Inertia::render('CompanyAdmin/LeaveType/Index', [
            'leaveTypes' => $leaveTypes
        ]);
        // This will print everything your controller is sending to the page
        //console.log(usePage().props);
    }
}
