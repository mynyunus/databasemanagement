<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductGroup;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $arabicJuice = ProductGroup::where('name', 'Jus Arabic Gold')->first();
        $supplements = ProductGroup::where('name', 'Health Supplements')->first();
        $beauty = ProductGroup::where('name', 'Beauty Products')->first();

        // Jus Arabic Gold products
        if ($arabicJuice) {
            $products = [
                [
                    'product_group_id' => $arabicJuice->id,
                    'name' => '1 Bottle',
                    'sku' => 'JAG-001',
                    'is_active' => true,
                    'price_default' => 45.00,
                    'cogs' => 25.00,
                    'postage_cost' => 8.00,
                    'bottle_qty' => 1,
                ],
                [
                    'product_group_id' => $arabicJuice->id,
                    'name' => '2 Bottles',
                    'sku' => 'JAG-002',
                    'is_active' => true,
                    'price_default' => 85.00,
                    'price_override' => 80.00,
                    'cogs' => 50.00,
                    'postage_cost' => 12.00,
                    'bottle_qty' => 2,
                ],
                [
                    'product_group_id' => $arabicJuice->id,
                    'name' => '3 Bottles',
                    'sku' => 'JAG-003',
                    'is_active' => true,
                    'price_default' => 125.00,
                    'price_override' => 115.00,
                    'cogs' => 75.00,
                    'postage_cost' => 15.00,
                    'bottle_qty' => 3,
                ],
            ];

            foreach ($products as $product) {
                Product::create($product);
            }
        }

        // Health Supplements
        if ($supplements) {
            Product::factory(8)->create([
                'product_group_id' => $supplements->id,
            ]);
        }

        // Beauty Products
        if ($beauty) {
            Product::factory(6)->create([
                'product_group_id' => $beauty->id,
            ]);
        }

        // Random products for other groups
        $otherGroups = ProductGroup::whereNotIn('name', [
            'Jus Arabic Gold', 'Health Supplements', 'Beauty Products'
        ])->get();

        foreach ($otherGroups as $group) {
            Product::factory(rand(3, 8))->create([
                'product_group_id' => $group->id,
            ]);
        }
    }
}