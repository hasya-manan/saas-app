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
        Schema::table('leave_tiers', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('leave_applications', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('staff_finances', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_tiers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('leave_applications', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('staff_finances', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
