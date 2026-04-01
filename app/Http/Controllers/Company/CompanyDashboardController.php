<?php

namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
//use Illuminate\Http\Request;

class CompanyDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return Inertia::render('CompanyAdmin/Dashboard', [
            'tenant' => $user->tenant,
            'stats' => [
               'total_employees' => User::where('tenant_id', $user->tenant_id)->count(),
                // Add more company-specific stats later
            ]
        ]);
    }
}
