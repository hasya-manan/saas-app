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
            // Adding Waris details to user_profiles
            $table->string('waris_ic')->unique()->nullable()->after('waris_relationship');
            $table->string('waris_phone')->nullable()->after('waris_ic');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            //
            $table->dropColumn(['waris_ic', 'waris_phone']);
        });
    }
};
