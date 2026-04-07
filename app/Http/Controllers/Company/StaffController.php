<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role; 
//use Illuminate\Http\Request;
use Inertia\Inertia;

class StaffController extends Controller
{
    //
    public function index()
    {

    /**
     * Display the list of staff (List.vue)
     */
        $employees = User::where('tenant_id', auth()->user()->tenant_id)
            ->with(['role', 'profile'])
            // ->withTrashed() // So we see the "Deactivated" ones too
            ->latest()
            ->paginate(10);

        return Inertia::render('CompanyAdmin/Staff/List', [
            'employees' => $employees
        ]);
    }

    /**
     * Show the form to add new staff (Create.vue)
     */
    public function create()
    {
        return Inertia::render('CompanyAdmin/Staff/Create', [
            // Pass roles so the Admin can choose (Staff, Manager, etc.)
            // Usually IDs 2 and 3 for Company Admin and Staff
            'roles' => Role::whereIn('id', [2, 3])->get()
        ]);
    }
}
