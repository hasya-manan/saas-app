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
            ['category' => 'relationship', 'key' => 'spouse', 'label' => 'Spouse (Husband/Wife)', 'description' => '', 'sort_order' => 10],
            ['category' => 'relationship', 'key' => 'parent', 'label' => 'Parent (Mother/Father)', 'description' => '', 'sort_order' => 20],
            ['category' => 'relationship', 'key' => 'child', 'label' => 'Child ', 'description' => '', 'sort_order' => 30],
            ['category' => 'relationship', 'key' => 'sibling', 'label' => 'Sibling ', 'description' => '', 'sort_order' => 40],
            ['category' => 'relationship', 'key' => 'other', 'label' => 'Other', 'description' => 'Custom relationship', 'sort_order' => 50],
            
            // Marital Status
            ['category' => 'marital_status', 'key' => 'single', 'label' => 'Single', 'description' => '', 'sort_order' => 10],
            ['category' => 'marital_status', 'key' => 'married', 'label' => 'Married', 'description' => '', 'sort_order' => 20],
            ['category' => 'marital_status', 'key' => 'divorced', 'label' => 'Divorced', 'description' => '', 'sort_order' => 30],
            ['category' => 'marital_status', 'key' => 'widowed', 'label' => 'Widowed', 'description' => '', 'sort_order' => 40],

        // States of Malaysia
            ['category' => 'state', 'key' => 'johor', 'label' => 'Johor', 'description' => '', 'sort_order' => 10],
            ['category' => 'state', 'key' => 'kedah', 'label' => 'Kedah', 'description' => '', 'sort_order' => 20],
            ['category' => 'state', 'key' => 'kelantan', 'label' => 'Kelantan', 'description' => '', 'sort_order' => 30], // Added this
            ['category' => 'state', 'key' => 'kuala_lumpur', 'label' => 'Kuala Lumpur', 'description' => '', 'sort_order' => 40],
            ['category' => 'state', 'key' => 'labuan', 'label' => 'Labuan', 'description' => '', 'sort_order' => 50],
            ['category' => 'state', 'key' => 'melaka', 'label' => 'Melaka', 'description' => '', 'sort_order' => 60],
            ['category' => 'state', 'key' => 'negeri_sembilan', 'label' => 'Negeri Sembilan', 'description' => '', 'sort_order' => 70],
            ['category' => 'state', 'key' => 'pahang', 'label' => 'Pahang', 'description' => '', 'sort_order' => 80],
            ['category' => 'state', 'key' => 'penang', 'label' => 'Penang', 'description' => '', 'sort_order' => 90],
            ['category' => 'state', 'key' => 'perak', 'label' => 'Perak', 'description' => '', 'sort_order' => 100],
            ['category' => 'state', 'key' => 'perlis', 'label' => 'Perlis', 'description' => '', 'sort_order' => 110],
            ['category' => 'state', 'key' => 'putrajaya', 'label' => 'Putrajaya', 'description' => '', 'sort_order' => 120],
            ['category' => 'state', 'key' => 'sabah', 'label' => 'Sabah', 'description' => '', 'sort_order' => 130],
            ['category' => 'state', 'key' => 'sarawak', 'label' => 'Sarawak', 'description' => '', 'sort_order' => 140],
            ['category' => 'state', 'key' => 'selangor', 'label' => 'Selangor', 'description' => '', 'sort_order' => 150],
            ['category' => 'state', 'key' => 'terengganu', 'label' => 'Terengganu', 'description' => '', 'sort_order' => 160],
            
            // Common Malaysian Banks (To fill the dropdown for staff_finances)
            ['category' => 'bank', 'key' => 'alliance_bank', 'label' => 'Alliance Bank', 'description' => '', 'sort_order' => 10],
            ['category' => 'bank', 'key' => 'ambank', 'label' => 'AmBank', 'description' => '', 'sort_order' => 20],
            ['category' => 'bank', 'key' => 'bank_islam', 'label' => 'Bank Islam', 'description' => '', 'sort_order' => 1],
            ['category' => 'bank', 'key' => 'cimb', 'label' => 'CIMB', 'description' => '', 'sort_order' => 30],
            ['category' => 'bank', 'key' => 'hong_leong_bank', 'label' => 'Hong Leong Bank', 'description' => '', 'sort_order' => 40],
            ['category' => 'bank', 'key' => 'hsbc', 'label' => 'HSBC', 'description' => '', 'sort_order' => 50],
            ['category' => 'bank', 'key' => 'maybank', 'label' => 'Maybank', 'description' => '', 'sort_order' => 60],
            ['category' => 'bank', 'key' => 'public_bank', 'label' => 'Public Bank', 'description' => '', 'sort_order' => 70],
            ['category' => 'bank', 'key' => 'rhb', 'label' => 'RHB', 'description' => '', 'sort_order' => 80],
            ['category' => 'bank', 'key' => 'uob', 'label' => 'UOB', 'description' => '', 'sort_order' => 90],
            ['category' => 'bank', 'key' => 'standard_chartered', 'label' => 'Standard Chartered', 'description' => '', 'sort_order' => 100],
            
            
        
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
