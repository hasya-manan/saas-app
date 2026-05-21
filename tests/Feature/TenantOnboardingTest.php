<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
//use App\Models\LeaveType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantOnboardingTest extends TestCase
{
    use RefreshDatabase;

    //  This automatically runs your DatabaseSeeder (Roles + Super Admin)
    protected bool $seed = true;

    public function test_super_admin_can_successfully_onboard_new_tenant_with_default_settings(): void
    {
        // 1. Arrange: Simply pull the Super Admin user that your seeder just created!
        $authenticatedSuperAdmin = User::where('email', 'superadmin@example.com')->firstOrFail();

        $payload = [
            'company_name'  => 'ACME International',
            'company_email' => 'operations@acme.com',
            'admin_name'    => 'John Doe',
            'admin_email'   => 'john.doe@acme.com',
        ];

        // 2. Act: Log in as that seeder-created Super Admin
        $response = $this->actingAs($authenticatedSuperAdmin)
                         ->post(route('tenants.store'), $payload);

        // 3. Assert
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tenants', [
            'company_name' => 'ACME International',
            'email'        => 'operations@acme.com',
        ]);

        $tenant = Tenant::where('email', 'operations@acme.com')->firstOrFail();

        $this->assertDatabaseHas('users', [
            'name'      => 'John Doe',
            'email'     => 'john.doe@acme.com',
            'tenant_id' => $tenant->id,
            'role_id'   => 2,
        ]);

        $this->assertDatabaseHas('leave_types', [
            'tenant_id' => $tenant->id,
            'code'      => 'AL',
        ]);
    }
}