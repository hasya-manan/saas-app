<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Always seed roles first so users have something to link to!
        $this->call([
            GlobalLookupSeeder::class,
            RoleSeeder::class,
        ]);

        // 2. Create your SuperAdmin account
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password123'),
            'role_id' => 1,
            'tenant_id' => null,
        ]);
    
        echo "SuperAdmin account created successfully!";
    }
}
