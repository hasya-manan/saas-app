<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeaveTypeController extends Controller
{
     public function index(Request $request): Response
    {
        return Inertia::render('CompanyAdmin/LeaveType/Index');
    }
}
