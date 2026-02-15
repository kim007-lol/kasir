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
            $table->unsignedBigInteger('warehouse_item_id')->nullable()->change();
            $table->unsignedBigInteger('category_id')->nullable()->change();
            $table->unsignedBigInteger('supplier_id')->nullable()->change();
            $table->boolean('is_consignment')->default(false)->after('stock');
            $table->string('consignment_source')->nullable()->after('is_consignment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cashier_items', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_item_id')->nullable(false)->change();
            $table->unsignedBigInteger('category_id')->nullable(false)->change();
            $table->unsignedBigInteger('supplier_id')->nullable(false)->change();
            $table->dropColumn(['is_consignment', 'consignment_source']);
        });
    }
};
