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
        //
        // Rename in Departments
        Schema::table('departments', function (Blueprint $table) {
            $table->renameColumn('manager_id', 'hod_id');
        });

        // Rename in Users
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('manager_id', 'supervisor_id');
     });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        // Put the names back to 'manager_id' in Departments
        Schema::table('departments', function (Blueprint $table) {
            $table->renameColumn('hod_id', 'manager_id');
        });

        // Put the names back to 'manager_id' in Users
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('supervisor_id', 'manager_id');
        });
    }
};
