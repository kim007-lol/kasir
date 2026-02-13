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
        Schema::table('cashier_items', function (Blueprint $table) {
            $table->decimal('discount', 15, 2)->default(0)->change();
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            $table->decimal('discount', 15, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cashier_items', function (Blueprint $table) {
            $table->decimal('discount', 5, 2)->default(0)->change();
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            $table->decimal('discount', 5, 2)->default(0)->change();
        });
    }
};
