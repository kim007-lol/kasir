<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure column exists to avoid errors during development if migration hasn't run yet contextually
        // but strictly we expect migration to run first.

        User::updateOrCreate(
            ['email' => 'devidiana@gmail.com'],
            [
                'name' => 'Devi Diana Safitri, SPd.',
                'username' => 'admin',
                'password' => Hash::make('adminsmegabiz'),
                'role' => 'admin'
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir123@gmail.com'],
            [
                'name' => 'Kasir SMEGABIZ',
                'username' => 'kasir',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir'
            ]
        );
    }
}
