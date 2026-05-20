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
       Schema::create('leave_balances', function (Blueprint $table) {
        $table->id();
        $table->string('tenant_id');
        $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('leave_type_id')->constrained()->onDelete('cascade');
        $table->integer('year'); // e.g., 2026, 2027 (Crucial for annual resets!)
        
        // The core calculation blocks
        $table->decimal('allotted_days', 4, 1);     // Dynamic tier days (e.g., 14.0)
        $table->decimal('carried_forward', 4, 1)->default(0.0); // Days rolled over from last year (e.g., 3.5)
        $table->decimal('taken_days', 4, 1)->default(0.0);      // Sum of approved leave applications (e.g., 2.0)
        
        $table->timestamps();
        
        // 🛡️ THE DATA INTEGRITY GUARD: Prevents duplicate balance rows.
        // Without this: A user double-clicking or a network lag could insert two balance rows for 
        // the same year (e.g., 2026 AL = 14 days, and another 2026 AL = 14 days). The system would 
        // sum them up and accidentally grant the employee 28 days of leave.
        // With this: The database strictly blocks duplicates at the hardware level, throwing an 
        // immediate error if a bug attempts to corrupt the employee's official wallet balance.
        $table->unique(['user_id', 'leave_type_id', 'year']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};
