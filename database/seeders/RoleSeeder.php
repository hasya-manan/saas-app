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
        \App\Models\Role::firstOrCreate(['name' => 'superadmin']);
        \App\Models\Role::firstOrCreate(['name' => 'admin_company']);
        \App\Models\Role::firstOrCreate(['name' => 'staff']);
    }
}
