<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_group_id');
            $table->string('name');
            $table->string('sku')->unique();
            $table->boolean('is_active')->default(true);
            $table->decimal('price_default', 12, 2);
            $table->decimal('price_override', 12, 2)->nullable();
            $table->decimal('cogs', 12, 2)->default(0);
            $table->decimal('postage_cost', 12, 2)->default(0);
            $table->integer('bottle_qty')->default(1);
            $table->timestamps();
            
            $table->foreign('product_group_id')->references('id')->on('product_groups')->onDelete('cascade');
            $table->index(['product_group_id']);
            $table->index(['sku', 'is_active']);
            $table->index(['name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};