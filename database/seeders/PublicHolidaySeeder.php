<?php

namespace Database\Seeders;

use App\Models\PublicHoliday;
//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PublicHolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $publicHolidays = [
            // National Holidays (state = null)
            
            [
                'date' => '2026-01-01',
                'observed_date' => '2026-01-01',
                'name' => "New Year's Day",
                'state' => null,
                'is_recurring' => true,
            ],
            [
                'date' => '2026-01-26',
                'observed_date' => '2026-01-26',
                'name' => "Republic Day",
                'state' => null,
                'is_recurring' => true,
            ],
            [
                'date' => '2026-02-01',
                'observed_date' => '2026-02-01',
                'name' => "Federal Territory Day",
                'state' => 'Kuala Lumpur',
                'is_recurring' => true,
            ],
            [
                'date' => '2026-05-01',
                'observed_date' => '2026-05-01',
                'name' => "Labour Day",
                'state' => null,
                'is_recurring' => true,
            ],
            [
                'date' => '2026-08-31',
                'observed_date' => '2026-08-31',
                'name' => "National Day",
                'state' => null,
                'is_recurring' => true,
            ],
            [
                'date' => '2026-09-16',
                'observed_date' => '2026-09-16',
                'name' => "Malaysia Day",
                'state' => null,
                'is_recurring' => true,
            ],
            [
                'date' => '2026-12-25',
                'observed_date' => '2026-12-25',
                'name' => "Christmas Day",
                'state' => null,
                'is_recurring' => true,
            ],
            
            // State-Specific Holidays
            [
                'date' => '2026-02-11',
                'observed_date' => '2026-02-11',
                'name' => "Thaipusam",
                'state' => 'Selangor',
                'is_recurring' => true,
            ],
            [
                'date' => '2026-02-11',
                'observed_date' => '2026-02-11',
                'name' => "Thaipusam",
                'state' => 'Kuala Lumpur',
                'is_recurring' => true,
            ],
            [
                'date' => '2026-02-11',
                'observed_date' => '2026-02-11',
                'name' => "Thaipusam",
                'state' => 'Johor',
                'is_recurring' => true,
            ],
            [
                'date' => '2026-02-11',
                'observed_date' => '2026-02-11',
                'name' => "Thaipusam",
                'state' => 'Penang',
                'is_recurring' => true,
            ],
            [
                'date' => '2026-07-07',
                'observed_date' => '2026-07-07',
                'name' => "Penang Heritage Day",
                'state' => 'Penang',
                'is_recurring' => true,
            ],
            [
                'date' => '2026-12-11',
                'observed_date' => '2026-12-11',
                'name' => "Sultan of Selangor's Birthday",
                'state' => 'Selangor',
                'is_recurring' => true,
            ],
        ];

        foreach ($publicHolidays as $holiday) {
            PublicHoliday::create([
                'date' => $holiday['date'],
                'observed_date' => $holiday['observed_date'] ?? $holiday['date'],
                'name' => $holiday['name'],
                'state' => $holiday['state'],
                'is_recurring' => $holiday['is_recurring'],
                'is_active' => true,
            ]);
        }
    }
}
