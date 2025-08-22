<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerPurchase;
use App\Models\CustomerPurchaseItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CustomerPurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $products = Product::active()->get();

        foreach ($customers as $customer) {
            // Each customer has 1-5 purchases
            $purchaseCount = rand(1, 5);
            
            for ($i = 0; $i < $purchaseCount; $i++) {
                $purchase = CustomerPurchase::factory()->create([
                    'customer_id' => $customer->id,
                ]);

                // Each purchase has 1-3 items
                $itemCount = rand(1, 3);
                $totalAmount = 0;

                for ($j = 0; $j < $itemCount; $j++) {
                    $product = $products->random();
                    $quantity = rand(1, 3);
                    $priceEach = $product->final_price;
                    
                    CustomerPurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price_each' => $priceEach,
                    ]);

                    $totalAmount += $quantity * $priceEach;
                }

                // Update purchase total
                $purchase->update(['total_amount' => $totalAmount]);
            }
        }
    }
}