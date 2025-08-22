<?php

namespace Database\Seeders;

use App\Models\ProductGroup;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductGroupSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@business.com')->first();

        // Create specific product groups for Malaysian business
        $groups = [
            [
                'name' => 'Jus Arabic Gold',
                'description' => 'Premium Arabic juice collection with gold benefits',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Health Supplements',
                'description' => 'Natural health supplements and vitamins',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Beauty Products',
                'description' => 'Skincare and beauty enhancement products',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Traditional Herbs',
                'description' => 'Traditional Malaysian and Arabic herbs',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
        ];

        foreach ($groups as $group) {
            ProductGroup::create($group);
        }

        // Create additional random groups
        ProductGroup::factory(6)->create([
            'created_by' => $admin->id,
        ]);
    }
}