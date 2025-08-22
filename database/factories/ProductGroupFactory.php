<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductGroupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true) . ' Products',
            'description' => $this->faker->sentence(),
            'is_active' => $this->faker->boolean(85), // 85% chance active
            'created_by' => User::factory(),
        ];
    }

    public function active()
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}