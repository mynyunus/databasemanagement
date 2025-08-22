<?php

namespace Database\Factories;

use App\Models\ProductGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $price = $this->faker->randomFloat(2, 10, 500);
        
        return [
            'product_group_id' => ProductGroup::factory(),
            'name' => $this->faker->words(3, true),
            'sku' => strtoupper($this->faker->unique()->bothify('???-###')),
            'is_active' => $this->faker->boolean(90),
            'price_default' => $price,
            'price_override' => $this->faker->boolean(30) ? $price * 0.9 : null,
            'cogs' => $price * 0.6,
            'postage_cost' => $this->faker->randomFloat(2, 5, 25),
            'bottle_qty' => $this->faker->numberBetween(1, 6),
        ];
    }

    public function active()
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}