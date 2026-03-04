<?php

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB; // Add this
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('superadmin can create a company and its admin user simultaneously', function () {
    // 1. MUST seed the roles first so the Foreign Key doesn't fail
    DB::table('roles')->insert([
        ['id' => 1, 'name' => 'superadmin'],
        ['id' => 2, 'name' => 'admin_company'],
    ]);

    // 2. Setup: Create the SuperAdmin (Role 1)
    $superAdmin = User::factory()->create([
        'name'      => 'Super Admin',
        'email'     => 'admin@pakitangan.com',
        'role_id'   => 1,
        'tenant_id' => null, // SuperAdmin has no tenant
    ]);

    // 3. Action: Post the data
    $response = $this->actingAs($superAdmin)
        ->post(route('tenants.store'), [
            'company_name' => 'Kakitangan Client A',
            'admin_name'   => 'Client Admin User',
            'admin_email'  => 'client@company.com',
        ]);

    // 4. Assertions
    $response->assertRedirect(route('superadmin.dashboard'));

    // Check Tenant exists
    $tenant = Tenant::where('company_name', 'Kakitangan Client A')->first();
    expect($tenant)->not->toBeNull();

    // Check Company Admin exists and is linked
    $companyAdmin = User::where('email', 'client@company.com')->first();
    expect($companyAdmin)->not->toBeNull()
        ->and($companyAdmin->tenant_id)->toBe($tenant->id)
        ->and($companyAdmin->role_id)->toBe(2);
});