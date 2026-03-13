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
        Schema::create('global_lookups', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // e.g., 'tenant_status', 'gender'
            $table->string('key');      // e.g., 'active', 'pending'
            $table->string('label');    // e.g., 'Active User', 'Awaiting Approval'
            $table->integer('sort_order')->default(0); // To control which shows first in the select
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        
        // Indexing for faster searching
        $table->index(['category', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_lookups');
    }
};
