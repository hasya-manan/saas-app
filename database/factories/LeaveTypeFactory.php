<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveType>
 */
class LeaveTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
           // tenant_id will be passed in the test, but you can provide a default if needed
            'name' => $this->faker->word(),
            'code' => strtoupper($this->faker->lexify('???')), // Fills the required 'code' field (e.g., "ALW")
            'is_calculated_by_experience' => false,
            'default_days' => 14, 
            'allows_carry_forward' => false,
            'probation_period_months' => 3,
            'is_pro_rata' => false,
            'is_active' => true,
            'requires_attachment' => false,
        ];
    }
}
