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
        Schema::create('leave_types', function (Blueprint $table) {
        $table->id();
        // Assuming your company/tenant table is called 'tenants'
        $table->foreignId('tenant_id')->constrained()->onDelete('cascade'); 
        $table->string('name'); // e.g., "Annual Leave"
        $table->string('code'); // e.g., "AL"
        $table->boolean('is_calculated_by_experience')->default(false);
        $table->integer('default_days')->nullable(); // Used if is_calculated_by_experience is false
        $table->boolean('allows_carry_forward')->default(false);
        $table->timestamps();
        // Allows both Tenant 5 and Tenant 6 to have an 'AL' code safely, but blocks duplicates inside a single company
        $table->unique(['tenant_id', 'code']);
        
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
