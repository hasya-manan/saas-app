<?php

use App\Models\Tenant;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveTier;
use App\Models\Department;
use App\Models\Role;
use Database\Seeders\RoleSeeder; // Import your seeder

test('it automatically creates leave balances when a new staff is registered', function () {
    // 1. SETUP: Seed roles so foreign key constraints on role_id won't fail
    $this->seed(RoleSeeder::class);

    // Create tenant
    $tenant = Tenant::factory()->create();

    // Get the ID of the 'admin_company' role dynamically
    $adminRole = Role::where('name', 'admin_company')->first();

    // Create the admin user using the seeded role ID
    $admin = User::factory()->create([
        'tenant_id' => $tenant->id,
        'role_id'   => $adminRole->id, 
    ]);

    // Create a department for this tenant
    $department = Department::create([
        'tenant_id' => $tenant->id,
        'name'      => 'Development & Management',
    ]);

    // Setup Leave Type and Tier
    $leaveType = LeaveType::create([
        'tenant_id' => $tenant->id,
        'name' => 'Annual Leave',
        'code' => 'AL',
        'is_calculated_by_experience' => 1,
        'default_days' => 14,
        'is_active' => 1,
    ]);

    LeaveTier::create([
        'tenant_id' => $tenant->id,
        'leave_type_id' => $leaveType->id,
        'min_years' => 0.00,
        'max_years' => 2.00,
        'allowed_days' => 12.00,
        'max_carry_forward_days' => 5.00,
    ]);

    // 2. ACTION: Send request to store a new staff member
    $response = $this->actingAs($admin)->post(route('admin_company.users.store'), [
        'name'                  => 'Jane Smith',
        'email'                 => 'jane@example.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
        'role_id'               => Role::where('name', 'staff')->first()->id,
        'department_id'         => $department->id,

        // user_profiles
        'ic_number'          => '950202-14-1234',
        'position'           => 'Developer',
        'join_date'          => now()->subMonths(6)->toDateString(),
        'waris_relationship' => 'parent',

        // staff_finances
        'basic_salary'       => 4000,
        'epf_rate_employee'  => 11,
        'epf_rate_employer'  => 13,
        'socso_type'         => 'First Category',
        'eis_enabled'        => true,
    ]);

    // 3. ASSERT
    $response->assertSessionHasNoErrors();
    
    $createdUser = User::where('email', 'jane@example.com')->first();
    expect($createdUser)->not->toBeNull();

    // Check leave balance using the database record
    $this->assertDatabaseHas('leave_balances', [
        'tenant_id'     => $tenant->id,
        'user_id'       => $createdUser->id,
        'leave_type_id' => $leaveType->id,
        'year'          => date('Y'),
        'taken_days'    => 0.00,
    ]);
});