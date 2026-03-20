<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            // Target: 'cashier' (stok kasir) atau 'warehouse' (stok gudang)
            $table->string('target', 20)->default('cashier')->after('cashier_item_id');
            // FK ke warehouse_items (nullable, hanya terisi jika target = warehouse)
            $table->unsignedBigInteger('warehouse_item_id')->nullable()->after('target');
            // Buat cashier_item_id nullable (karena sekarang bisa adjust gudang saja)
            $table->unsignedBigInteger('cashier_item_id')->nullable()->change();

            $table->foreign('warehouse_item_id')->references('id')->on('warehouse_items')->nullOnDelete();
            $table->index('warehouse_item_id');
            $table->index('target');
        });
    }

    public function down(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropForeign(['warehouse_item_id']);
            $table->dropIndex(['warehouse_item_id']);
            $table->dropIndex(['target']);
            $table->dropColumn(['target', 'warehouse_item_id']);
        });
    }
};
