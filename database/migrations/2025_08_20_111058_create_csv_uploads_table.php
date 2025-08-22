<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('csv_uploads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Laravel way
            $table->enum('type', ['customers', 'purchases']);
            $table->string('original_filename');
            $table->integer('row_count')->default(0);
            $table->enum('status', ['pending', 'processed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'type']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('csv_uploads');
    }
};