<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cashier_items', function (Blueprint $table) {
            $table->decimal('cost_price', 15, 2)->nullable()->default(0)->after('selling_price');
        });
    }

    public function down(): void
    {
        Schema::table('cashier_items', function (Blueprint $table) {
            $table->dropColumn('cost_price');
        });
    }
};
