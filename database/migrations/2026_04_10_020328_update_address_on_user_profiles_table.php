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
        // Now $table is defined and usable here
        $table->dropColumn('address');

        $table->string('address_line_1')->nullable()->after('phone');
        $table->string('address_line_2')->nullable()->after('address_line_1');
        $table->string('city')->nullable()->after('address_line_2');
        $table->string('postcode', 10)->nullable()->after('city');
        $table->string('state')->nullable()->after('postcode');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the changes
            $table->text('address')->nullable()->after('phone');
            
            $table->dropColumn(['address_line_1', 'address_line_2', 'city', 'postcode', 'state']);
    }
};
