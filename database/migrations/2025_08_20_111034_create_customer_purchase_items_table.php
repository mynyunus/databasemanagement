<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_purchase_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('purchase_id');
            $table->uuid('product_id')->nullable();
            $table->decimal('quantity', 12, 3);
            $table->string('unit')->nullable();
            $table->decimal('price_each', 12, 2);
            $table->decimal('subtotal', 12, 2)->storedAs('quantity * price_each');
            $table->timestamps();
            
            $table->foreign('purchase_id')->references('id')->on('customer_purchases')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_purchase_items');
    }
};