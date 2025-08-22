<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('phone_raw');
            $table->string('phone_e164')->unique();
            $table->string('email')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode')->nullable();
            $table->enum('state', [
                'Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 
                'Pahang', 'Perak', 'Perlis', 'Pulau Pinang', 'Sabah', 
                'Sarawak', 'Selangor', 'Terengganu', 'W.P. Kuala Lumpur', 
                'W.P. Labuan', 'W.P. Putrajaya'
            ])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['phone_e164']);
            $table->index(['state']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};