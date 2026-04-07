<?php

namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $roles = [
        ['name' => 'superadmin', 'display_name' => 'System Administrator'],
        ['name' => 'admin_company', 'display_name' => 'Company Admin'],
        ['name' => 'staff', 'display_name' => 'Staff'],
    ];

    foreach ($roles as $role) {
        \App\Models\Role::updateOrCreate(
            ['name' => $role['name']], // Search criteria
            ['display_name' => $role['display_name']] // Values to update/create
        );
    }
}
}
