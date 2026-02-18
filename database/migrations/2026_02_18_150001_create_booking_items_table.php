<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('cashier_item_id')->constrained()->onDelete('cascade');
            $table->string('name'); // snapshot nama item saat pesan
            $table->integer('qty');
            $table->decimal('price', 15, 2); // snapshot harga saat pesan
            $table->decimal('subtotal', 15, 2);
            $table->text('notes')->nullable(); // catatan per item, misal "tidak pedas"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_items');
    }
};
