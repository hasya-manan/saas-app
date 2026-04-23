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
            // Tenant Status
            ['category' => 'tenant_status', 'key' => 'active', 'label' => 'Active', 'description' => 'Tenant is active', 'sort_order' => 10],
            ['category' => 'tenant_status', 'key' => 'inactive', 'label' => 'Inactive', 'description' => 'Tenant is inactive', 'sort_order' => 20],   
            ['category' => 'tenant_status', 'key' => 'deactivated', 'label' => 'Deactivated','description' => 'Tenant is deactivated ', 'sort_order' => 30],
            ['category' => 'tenant_status', 'key' => 'suspended', 'label' => 'Suspended', 'description' => 'Tenant is suspended', 'sort_order' => 40],
            // Gender
            ['category' => 'gender', 'key' => 'm', 'label' => 'Male', 'description' => 'Gender is Male', 'sort_order' => 10],
            ['category' => 'gender', 'key' => 'f', 'label' => 'Female', 'description' => 'Gender is Female', 'sort_order' => 20],
            // SOCSO Type 
            ['category' => 'socso_type', 'key' => 'category_1', 'label' => 'Category 1', 'description' => 'Employment Injury & Invalidity (Under 60 years old)', 'sort_order' => 10],
            ['category' => 'socso_type', 'key' => 'category_2', 'label' => 'Category 2', 'description' => 'Employment Injury Only (60 years old and above)', 'sort_order' => 20],
            
            // next of kin
            // Waris Relationships
            ['category' => 'relationship', 'key' => 'spouse', 'label' => 'Spouse (Husband/Wife)', 'description' => '', 'sort_order' => 10],
            ['category' => 'relationship', 'key' => 'parent', 'label' => 'Parent (Mother/Father)', 'description' => '', 'sort_order' => 20],
            ['category' => 'relationship', 'key' => 'child', 'label' => 'Child ', 'description' => '', 'sort_order' => 30],
            ['category' => 'relationship', 'key' => 'sibling', 'label' => 'Sibling ', 'description' => '', 'sort_order' => 40],
            ['category' => 'relationship', 'key' => 'other', 'label' => 'Other', 'description' => 'Custom relationship', 'sort_order' => 50],
            // TODO::  Common Malaysian Banks (To fill the dropdown for staff_finances)
        ];

        foreach ($data as $item) {
        // RATIONALE: Search by category and key. If found, update. If not, create.
        // This makes your seeder "Idempotent" (safe to run many times).
        GlobalLookup::updateOrCreate(
            ['category' => $item['category'], 'key' => $item['key']],
            $item
        );
    }
    }
}
