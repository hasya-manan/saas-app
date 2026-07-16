<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantScopeTest extends TestCase
{
    use RefreshDatabase;

  public function test_non_superadmin_with_null_tenant_id_sees_nothing()
{
    // 1. Create a user object in memory, but DO NOT save it to the DB
    $nullTenantUser = new \App\Models\User([
        'tenant_id' => null, 
        'role_id' => 3,
        'name' => 'Test User'
    ]);

   //not insert to db temp because constrait FK tenant_id
    $this->actingAs($nullTenantUser);

    // 3. The Test: User should see 0 users
    $users = \App\Models\User::all();
    
    $this->assertCount(0, $users, 'A non-admin with a null tenant_id should see zero records.');
}
}