<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfer_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_item_id')->constrained('warehouse_items')->onDelete('cascade');
            $table->foreignId('cashier_item_id')->nullable()->constrained('cashier_items')->onDelete('set null');
            $table->string('item_name');
            $table->string('item_code');
            $table->integer('quantity');
            $table->enum('type', ['transfer_in', 'transfer_out', 'edit_increase', 'edit_decrease', 'delete_return']);
            $table->string('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_logs');
    }
};
