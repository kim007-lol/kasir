<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = Carbon::now();

        // Seed Admin User
        DB::table('users')->updateOrInsert(
            ['email' => 'devidiana@gmail.com'],
            [
                'name' => 'Devi Diana Safitri, SPd.',
                'username' => 'admin',
                'password' => Hash::make('adminsmegabiz'),
                'role' => 'admin',
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        // Seed Cashier User
        DB::table('users')->updateOrInsert(
            ['email' => 'kasir123@gmail.com'],
            [
                'name' => 'Kasir SMEGABIZ',
                'username' => 'kasir',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->whereIn('email', ['devidiana@gmail.com', 'kasir123@gmail.com'])->delete();
    }
};
