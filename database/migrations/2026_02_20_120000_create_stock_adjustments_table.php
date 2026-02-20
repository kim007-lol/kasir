<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashier_item_id')->constrained('cashier_items')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['increase', 'decrease']);
            $table->unsignedInteger('quantity');
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->string('reason'); // hilang, rusak, salah_input, stock_opname, lainnya
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('cashier_item_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
