<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Increase precision for price columns to support values up to 9,999,999,999,999.99
     */
    public function up(): void
    {
        Schema::table('warehouse_items', function (Blueprint $table) {
            $table->decimal('purchase_price', 15, 2)->default(0)->change();
            $table->decimal('selling_price', 15, 2)->default(0)->change();
            $table->decimal('discount', 15, 2)->default(0)->change();
        });

        Schema::table('cashier_items', function (Blueprint $table) {
            $table->decimal('selling_price', 15, 2)->default(0)->change();
            $table->decimal('cost_price', 15, 2)->nullable()->default(0)->change();
            $table->decimal('discount', 15, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_items', function (Blueprint $table) {
            $table->decimal('purchase_price', 10, 2)->default(0)->change();
            $table->decimal('selling_price', 10, 2)->default(0)->change();
            $table->decimal('discount', 10, 2)->default(0)->change();
        });

        Schema::table('cashier_items', function (Blueprint $table) {
            $table->decimal('selling_price', 10, 2)->default(0)->change();
            $table->decimal('cost_price', 10, 2)->nullable()->default(0)->change();
            $table->decimal('discount', 10, 2)->default(0)->change();
        });
    }
};
