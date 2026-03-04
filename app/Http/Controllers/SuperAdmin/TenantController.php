<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TenantController extends Controller
{
    public function index()
    {
        return Inertia::render('SuperAdmin/Tenants/Create');
    }

   public function store(Request $request)
    {
        $validated = $request->validate([
        'company_name' => 'required|string|max:255',
        'admin_name'   => 'required|string|max:255',
        'admin_email'  => 'required|email|unique:users,email',
    ]);

        // Logic: Keep generating until ID is unique
        do {
            $customId = 't-' . rand(100, 999) . Str::lower(Str::random(3));
        } while (Tenant::where('id', $customId)->exists()); 

        // Create the Tenant record
       $tenant = Tenant::create([
        'id'           => $customId,
        'company_name' => $validated['company_name'],
        'status'       => 'active', 
        ]);

        // TODO: if wants to have a domain for each tenant
        // $tenant->domains()->create([
        //     'domain' => $customId . '.localhost'
        // ]);

       
        User::create([
            'name'      => $validated['admin_name'],
            'email'     => $validated['admin_email'],
            'password'  => bcrypt('password123'), // TODO::maybe we can make their phone number as their password 
            'tenant_id' => $tenant->id,           // Link them to the new company
            'role_id'   => 2,                     // ROLE 2 = Company Admin
        ]);

        return redirect()->route('superadmin.dashboard')
            ->with('message', "Company $customId and Admin created!");
    }
}