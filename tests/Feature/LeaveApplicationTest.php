<?php

use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('prevent concurrent leave submissions from bypassing balance limits', function () {
    // 1. Run your RoleSeeder so the roles (including 'staff' with ID 3) exist in the test DB
    $this->seed(RoleSeeder::class);

    // 2. Setup tenant
    $tenant = \App\Models\Tenant::factory()->create();

    // 3. Setup user with role_id 3 (Staff)
    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'role_id' => 3, 
    ]);
    
    $leaveType = LeaveType::factory()->create([
        'tenant_id' => $tenant->id, 
        'requires_attachment' => false
    ]);

    // Give the user exactly 5 days of balance
    LeaveBalance::create([
        'tenant_id' => $user->tenant_id,
        'user_id' => $user->id,
        'leave_type_id' => $leaveType->id,
        'year' => date('Y'),
        'allotted_days' => 5,
        'carried_forward' => 0,
        'taken_days' => 0,
    ]);

    $payload = [
        'leave_type_id' => $leaveType->id,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addDays(4)->toDateString(),
        'leave_duration' => 'full',
        'reason' => 'Testing race condition',
    ];

    // 4. Simulate concurrent/back-to-back requests
    $response1 = $this->actingAs($user)->post(route('staff.applyLeave.store'), $payload);

    $response1->assertSessionHasNoErrors();

    $response2 = $this->actingAs($user)->post(route('staff.applyLeave.store'), $payload);

    $response2->assertSessionHas('errors');
    expect(session('errors')->getBag('default')->first())->toBe('Insufficient leave balance for this request.');

    $this->assertDatabaseCount('leave_applications', 1);
    
    $this->assertDatabaseHas('leave_balances', [
        'user_id' => $user->id,
        'taken_days' => 5,
    ]);
});