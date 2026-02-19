<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default values
        DB::table('shop_settings')->insert([
            ['key' => 'open_hour', 'value' => '07:00', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'close_hour', 'value' => '15:00', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'shop_open_override', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_settings');
    }
};
