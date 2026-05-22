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
        Schema::table('leave_types', function (Blueprint $table) {
            $table->integer('probation_period_months')->default(3)->after('allows_carry_forward'); 
            $table->boolean('is_pro_rata')->default(false)->after('probation_period_months');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
             $table->dropColumn(['probation_period_months', 'pro_rate']);
        });
    }
};
