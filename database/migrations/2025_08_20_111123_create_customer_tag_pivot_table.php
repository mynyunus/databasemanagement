<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_tag_pivot', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('tag_id');  // Change this to simple name
            $table->timestamps();
            
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('customer_tags')->onDelete('cascade');  // Update foreign key
            $table->unique(['customer_id', 'tag_id']);  // Now this matches
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_tag_pivot');
    }
};