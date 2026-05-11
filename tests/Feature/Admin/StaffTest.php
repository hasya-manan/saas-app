<?php

use App\Models\Tenant;
use App\Models\User;

test('admin can update all employee segments at once', function () {
    // 1. SETUP
    $tenant = Tenant::factory()->create();
    $admin = User::factory()->create(['tenant_id' => $tenant->id, 'role_id' => 2]);
    $staff = User::factory()->create(['tenant_id' => $tenant->id]);

    // 2. ACTION: Submit one big array (just like your frontend form)
    $this->actingAs($admin)->put(route('staff.update', $staff->uuid), [
        // Table: users
        'name' => 'John Doe',
        'role_id' => 3,
        'department_id' => 1,

        // Table: user_profiles
        'ic_number' => '900101-14-5555',
        'phone' => '0123456789',
        'position' => 'Manager',

        // Table: staff_finances (If you handle this in updateStaff too)
        'bank_name' => 'Maybank',
        'basic_salary' => 5000,
    ]);

    // 3. ASSERT: Check each table individually
    $this->assertDatabaseHas('users', ['name' => 'John Doe']);
    $this->assertDatabaseHas('user_profiles', ['user_id'   => $staff->id,'ic_number' => '900101145555']);
    $this->assertDatabaseHas('staff_finances', ['bank_name' => 'Maybank']);
});
