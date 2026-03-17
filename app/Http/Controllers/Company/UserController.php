<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\User;
//use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    //
    public function index()
    {
        $companyId = auth()->user()->tenant_id;

        return Inertia::render('CompanyAdmin/Users/Index', [
            'employees' => User::where('tenant_id', $companyId)
                ->with('role')
                ->latest()
                ->paginate(10)
        ]);
    }
}
