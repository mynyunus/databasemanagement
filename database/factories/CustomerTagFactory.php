<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerTagFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'VIP Customer', 'New Customer', 'Regular Customer', 
                'Wholesale', 'Online', 'Walk-in', 'Referral',
                'High Value', 'Loyal Customer', 'Bulk Buyer'
            ]),
            'color' => $this->faker->hexColor(),
        ];
    }
}