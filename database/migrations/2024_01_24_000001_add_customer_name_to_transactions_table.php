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
        Schema::table('transactions', function (Blueprint $table) {
            // Check if user_id column doesn't exist, add it first
            if (!Schema::hasColumn('transactions', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained();
            }
            
            // Add customer_name column
            if (!Schema::hasColumn('transactions', 'customer_name')) {
                $table->string('customer_name')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('customer_name');
        });
    }
};
