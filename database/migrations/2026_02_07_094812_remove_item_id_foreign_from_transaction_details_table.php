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
        Schema::table('transaction_details', function (Blueprint $table) {
            // Drop the foreign key constraint
            // The default naming convention is table_column_foreign
            $table->dropForeign('transaction_details_item_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            // Re-add the foreign key constraint if rolled back
            // Assuming it references 'id' on 'items' table
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
        });
    }
};
