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
        Schema::create('state_weekend_configs', function (Blueprint $table) {
           $table->id();
            $table->string('state', 100)->unique();
            $table->string('country', 100)->default('Malaysia');
            
            // Weekend configuration
            $table->json('weekend_days'); // e.g., [6,0] or [5,6]
            $table->string('primary_rest_day', 10); // '0' for Sunday, '5' for Friday
            $table->string('secondary_rest_day', 10); // '6' for Saturday
            
            // Rollover rules
            $table->boolean('rollover_primary')->default(true);
            $table->boolean('rollover_secondary')->default(false);
            $table->string('rollover_target_primary', 10)->nullable(); // '1' for Monday, '4' for Thursday
            $table->string('rollover_target_secondary', 10)->nullable();
            
            // Additional settings
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Index for performance
            $table->index(['state', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
