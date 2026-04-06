<?php

namespace Database\Seeders;

use App\Models\GlobalLookup;
//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GlobalLookupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // WHY:: i do 10, 20 instead 1, 2 because lets said i wants to insert new data in between , i can do like 15
        $data = [
            ['category' => 'tenant_status', 'key' => 'active', 'label' => 'Active', 'sort_order' => 10],
            ['category' => 'tenant_status', 'key' => 'inactive', 'label' => 'Inactive', 'sort_order' => 20],
             ['category' => 'tenant_status', 'key' => 'archived', 'label' => 'Archived', 'sort_order' => 30],
            ['category' => 'tenant_status', 'key' => 'suspended', 'label' => 'Suspended', 'sort_order' => 40],
            ['category' => 'gender', 'key' => 'm', 'label' => 'Male', 'sort_order' => 10],
            ['category' => 'gender', 'key' => 'f', 'label' => 'Female', 'sort_order' => 20],
        ];

        foreach ($data as $item) {
            GlobalLookup::create($item);
        }
    }
}
