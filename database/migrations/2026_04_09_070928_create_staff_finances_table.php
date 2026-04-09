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
        Schema::create('staff_finances', function (Blueprint $table) {
        $table->id();
        // RATIONALE: Foreign key to users with 'cascade' so if a user is 
        // deleted, their finance record is cleaned up too.
        $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
        
        // Salary (12,2 allows for millions of RM)
        $table->decimal('basic_salary', 12, 2)->default(0);
        
        // Bank Details
        $table->string('bank_name')->nullable();
        $table->string('bank_account_no')->nullable();
        
        // EPF / KWSP
        $table->string('epf_no')->nullable();
        $table->decimal('epf_rate_employee', 5, 2)->default(11.00);
        $table->decimal('epf_rate_employer', 5, 2)->default(13.00);
        
        // SOCSO / PERKESO
        $table->string('socso_no')->nullable();
        $table->string('socso_type')->default('Category 1'); // Full or Age > 60
        
        // Tax & EIS
        $table->string('tax_no')->nullable();
        $table->boolean('eis_enabled')->default(true);
        
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_finances');
    }
};
