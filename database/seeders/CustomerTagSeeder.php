<?php

namespace Database\Seeders;

use App\Models\CustomerTag;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerTagSeeder extends Seeder
{
    public function run(): void
    {
        // Create predefined tags
        $tags = [
            ['name' => 'VIP Customer', 'color' => '#FFD700'],
            ['name' => 'New Customer', 'color' => '#32CD32'],
            ['name' => 'Regular Customer', 'color' => '#4169E1'],
            ['name' => 'Wholesale', 'color' => '#8A2BE2'],
            ['name' => 'Online', 'color' => '#FF6347'],
            ['name' => 'Walk-in', 'color' => '#20B2AA'],
        ];

        foreach ($tags as $tagData) {
            $tag = CustomerTag::create($tagData);
            
            // Manually insert into pivot table with UUID
            $customers = Customer::inRandomOrder()->limit(rand(5, 15))->get();
            
            foreach ($customers as $customer) {
                \DB::table('customer_tag_pivot')->insert([
                    'id' => Str::uuid(),
                    'customer_id' => $customer->id,
                    'tag_id' => $tag->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Create additional random tags
        CustomerTag::factory(4)->create()->each(function ($tag) {
            $customers = Customer::inRandomOrder()->limit(rand(3, 10))->get();
            
            foreach ($customers as $customer) {
                \DB::table('customer_tag_pivot')->insert([
                    'id' => Str::uuid(),
                    'customer_id' => $customer->id,
                    'tag_id' => $tag->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }
}