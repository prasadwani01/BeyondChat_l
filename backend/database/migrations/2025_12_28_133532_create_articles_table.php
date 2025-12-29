<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            
            // Phase 1 Data
            $table->string('title');
            $table->string('original_url')->unique(); // Prevent duplicates
            $table->longText('original_content'); // Use longText for large HTML bodies
            
            // Phase 2 Data (Nullable because they don't exist initially)
            $table->longText('enhanced_content')->nullable(); 
            $table->json('reference_sources')->nullable(); // Stores the Google links used
            
            // Status Tracking
            $table->enum('status', ['pending', 'processing', 'completed'])->default('pending');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};