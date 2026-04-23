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
        Schema::table('user_profiles', function (Blueprint $table) {
            //
            $table->string('ic_number')->nullable()->unique()->after('user_id');

            // Adding gender for the staff member
            $table->string('user_gender')->nullable()->after('ic_number');
        
            // Adding gender for the next of kin
            $table->string('waris_gender')->nullable()->after('waris_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            //
            $table->dropUnique(['ic_number']);
            $table->dropColumn(['ic_number','user_gender', 'waris_gender']);
        });
    }
};
