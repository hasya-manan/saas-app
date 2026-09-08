<?php

namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StateWeekendConfig;
class StateWeekendConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $states = [
            // ===== SATURDAY-SUNDAY WEEKEND STATES =====
            // Rule: Sunday → Monday, Saturday = NO rollover
          [
                'state' => 'Selangor',
                'weekend_days' => [6, 0],
                'primary_rest_day' => '6',
                'secondary_rest_day' => '0',
                'rollover_primary' => false,
                'rollover_secondary' => true,
                'rollover_target_secondary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            [
                'state' => 'Kuala Lumpur',
                'weekend_days' => [6, 0],
                'primary_rest_day' => '6',
                'secondary_rest_day' => '0',
                'rollover_primary' => false,
                'rollover_secondary' => true,
                'rollover_target_secondary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            [
                'state' => 'Johor',
                'weekend_days' => [6, 0],
                'primary_rest_day' => '6',
                'secondary_rest_day' => '0',
                'rollover_primary' => false,
                'rollover_secondary' => true,
                'rollover_target_secondary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            [
                'state' => 'Penang',
                'weekend_days' => [6, 0],
                'primary_rest_day' => '6',
                'secondary_rest_day' => '0',
                'rollover_primary' => false,
                'rollover_secondary' => true,
                'rollover_target_secondary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            [
                'state' => 'Perak',
                'weekend_days' => [6, 0],
                'primary_rest_day' => '6',
                'secondary_rest_day' => '0',
                'rollover_primary' => false,
                'rollover_secondary' => true,
                'rollover_target_secondary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            [
                'state' => 'Pahang',
                'weekend_days' => [6, 0],
                'primary_rest_day' => '6',
                'secondary_rest_day' => '0',
                'rollover_primary' => false,
                'rollover_secondary' => true,
                'rollover_target_secondary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            [
                'state' => 'Negeri Sembilan',
                'weekend_days' => [6, 0],
                'primary_rest_day' => '6',
                'secondary_rest_day' => '0',
                'rollover_primary' => false,
                'rollover_secondary' => true,
                'rollover_target_secondary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            [
                'state' => 'Melaka',
                'weekend_days' => [6, 0],
                'primary_rest_day' => '6',
                'secondary_rest_day' => '0',
                'rollover_primary' => false,
                'rollover_secondary' => true,
                'rollover_target_secondary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            [
                'state' => 'Sabah',
                'weekend_days' => [6, 0],
                'primary_rest_day' => '6',
                'secondary_rest_day' => '0',
                'rollover_primary' => false,
                'rollover_secondary' => true,
                'rollover_target_secondary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            [
                'state' => 'Sarawak',
                'weekend_days' => [6, 0],
                'primary_rest_day' => '6',
                'secondary_rest_day' => '0',
                'rollover_primary' => false,
                'rollover_secondary' => true,
                'rollover_target_secondary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            [
                'state' => 'Perlis',
                'weekend_days' => [6, 0],
                'primary_rest_day' => '6',
                'secondary_rest_day' => '0',
                'rollover_primary' => false,
                'rollover_secondary' => true,
                'rollover_target_secondary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            
            // ===== FRIDAY-SATURDAY WEEKEND STATES =====
            // Rule: Friday = Primary (No rollover), Saturday = Secondary 
           [
                'state' => 'Kedah',
                'weekend_days' => [5, 6],
                'primary_rest_day' => '6',        // Saturday = No rollover
                'secondary_rest_day' => '5',      // Friday = Rollover trigger
                'rollover_primary' => false,
                'rollover_secondary' => true,     // Enable rollover for Friday
                'rollover_target_secondary' => '0', // Adjust target day as needed (e.g., Sunday or Thursday)
                'notes' => 'Friday-Saturday weekend. Friday holidays rollover enabled.',
            ],
            [
                'state' => 'Kelantan',
                'weekend_days' => [5, 6],
                'primary_rest_day' => '6',
                'secondary_rest_day' => '5',
                'rollover_primary' => false,
                'rollover_secondary' => true,
                'rollover_target_secondary' => '0',
                'notes' => 'Friday-Saturday weekend. Friday holidays rollover enabled.',
            ],
            [
                'state' => 'Terengganu',
                'weekend_days' => [5, 6],
                'primary_rest_day' => '6',
                'secondary_rest_day' => '5',
                'rollover_primary' => false,
                'rollover_secondary' => true,
                'rollover_target_secondary' => '0',
                'notes' => 'Friday-Saturday weekend. Friday holidays rollover enabled.',
            ],
        ];

        foreach ($states as $state) {
            StateWeekendConfig::updateOrCreate(
                ['state' => $state['state']],
                $state
            );
        }
    }
}