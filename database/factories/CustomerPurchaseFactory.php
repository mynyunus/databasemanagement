<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerPurchaseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'purchase_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'total_amount' => $this->faker->randomFloat(2, 20, 1000),
            'notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    public function recent()
    {
        return $this->state(fn (array $attributes) => [
            'purchase_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ]);
    }
}