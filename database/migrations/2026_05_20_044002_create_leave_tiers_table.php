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
        Schema::create('leave_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained()->onDelete('cascade');
            $table->decimal('min_years', 5, 2); // e.g., 0.00
            $table->decimal('max_years', 5, 2); // e.g., 1.00 or 99.00
            $table->integer('allowed_days');
            $table->integer('max_carry_forward_days')->default(0);
            $table->timestamps();
            // Prevents a tenant from defining two rules starting at the exact same experience milestone
            $table->unique(['leave_type_id', 'min_years']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_tiers');
    }
};
