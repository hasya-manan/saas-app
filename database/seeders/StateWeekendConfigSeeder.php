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
                'primary_rest_day' => '0',
                'secondary_rest_day' => '6',
                'rollover_primary' => true,
                'rollover_secondary' => false,
                'rollover_target_primary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            [
                'state' => 'Kuala Lumpur',
                'weekend_days' => [6, 0],
                'primary_rest_day' => '0',
                'secondary_rest_day' => '6',
                'rollover_primary' => true,
                'rollover_secondary' => false,
                'rollover_target_primary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            [
                'state' => 'Johor',
                'weekend_days' => [6, 0],
                'primary_rest_day' => '0',
                'secondary_rest_day' => '6',
                'rollover_primary' => true,
                'rollover_secondary' => false,
                'rollover_target_primary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            [
                'state' => 'Penang',
                'weekend_days' => [6, 0],
                'primary_rest_day' => '0',
                'secondary_rest_day' => '6',
                'rollover_primary' => true,
                'rollover_secondary' => false,
                'rollover_target_primary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            [
                'state' => 'Perak',
                'weekend_days' => [6, 0],
                'primary_rest_day' => '0',
                'secondary_rest_day' => '6',
                'rollover_primary' => true,
                'rollover_secondary' => false,
                'rollover_target_primary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            [
                'state' => 'Pahang',
                'weekend_days' => [6, 0],
                'primary_rest_day' => '0',
                'secondary_rest_day' => '6',
                'rollover_primary' => true,
                'rollover_secondary' => false,
                'rollover_target_primary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            [
                'state' => 'Negeri Sembilan',
                'weekend_days' => [6, 0],
                'primary_rest_day' => '0',
                'secondary_rest_day' => '6',
                'rollover_primary' => true,
                'rollover_secondary' => false,
                'rollover_target_primary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            [
                'state' => 'Melaka',
                'weekend_days' => [6, 0],
                'primary_rest_day' => '0',
                'secondary_rest_day' => '6',
                'rollover_primary' => true,
                'rollover_secondary' => false,
                'rollover_target_primary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            [
                'state' => 'Sabah',
                'weekend_days' => [6, 0],
                'primary_rest_day' => '0',
                'secondary_rest_day' => '6',
                'rollover_primary' => true,
                'rollover_secondary' => false,
                'rollover_target_primary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            [
                'state' => 'Sarawak',
                'weekend_days' => [6, 0],
                'primary_rest_day' => '0',
                'secondary_rest_day' => '6',
                'rollover_primary' => true,
                'rollover_secondary' => false,
                'rollover_target_primary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            [
                'state' => 'Perlis',
                'weekend_days' => [6, 0],
                'primary_rest_day' => '0',
                'secondary_rest_day' => '6',
                'rollover_primary' => true,
                'rollover_secondary' => false,
                'rollover_target_primary' => '1',
                'notes' => 'Saturday-Sunday weekend. Sunday holidays rollover to Monday.',
            ],
            
            // ===== FRIDAY-SATURDAY WEEKEND STATES =====
            // Rule: Friday → Thursday, Saturday = NO rollover
            [
                'state' => 'Kedah',
                'weekend_days' => [5, 6],
                'primary_rest_day' => '5',
                'secondary_rest_day' => '6',
                'rollover_primary' => true,
                'rollover_secondary' => false,
                'rollover_target_primary' => '4',
                'notes' => 'Friday-Saturday weekend. Friday holidays rollover to Thursday.',
            ],
            [
                'state' => 'Kelantan',
                'weekend_days' => [5, 6],
                'primary_rest_day' => '5',
                'secondary_rest_day' => '6',
                'rollover_primary' => true,
                'rollover_secondary' => false,
                'rollover_target_primary' => '4',
                'notes' => 'Friday-Saturday weekend. Friday holidays rollover to Thursday.',
            ],
            [
                'state' => 'Terengganu',
                'weekend_days' => [5, 6],
                'primary_rest_day' => '5',
                'secondary_rest_day' => '6',
                'rollover_primary' => true,
                'rollover_secondary' => false,
                'rollover_target_primary' => '4',
                'notes' => 'Friday-Saturday weekend. Friday holidays rollover to Thursday.',
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
