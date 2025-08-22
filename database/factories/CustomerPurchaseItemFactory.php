<?php

namespace Database\Factories;

use App\Models\CustomerPurchase;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerPurchaseItemFactory extends Factory
{
    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(0, 1, 5);
        $priceEach = $this->faker->randomFloat(2, 15, 200);

        return [
            'purchase_id' => CustomerPurchase::factory(),
            'product_id' => Product::factory(),
            'quantity' => $quantity,
            'unit' => $this->faker->optional(0.5)->randomElement(['pcs', 'bottles', 'sets']),
            'price_each' => $priceEach,
        ];
    }
}