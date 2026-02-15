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

        $admin = User::where('email', 'devidiana@gmail.com')->first();
        if ($admin) {
            $admin->update([
                'name' => 'Devi Diana Safitri, SPd.',
                'username' => 'admin',
                'password' => Hash::make('adminsmegabiz'),
                'role' => 'admin'
            ]);
        } else {
            User::create([
                'name' => 'Devi Diana Safitri, SPd.',
                'username' => 'admin',
                'email' => 'devidiana@gmail.com',
                'password' => Hash::make('adminsmegabiz'),
                'role' => 'admin'
            ]);
        }

        $kasir = User::where('email', 'kasir123@gmail.com')->first();
        if ($kasir) {
            $kasir->update([
                'name' => 'Kasir SMEGABIZ',
                'username' => 'kasir',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir'
            ]);
        } else {
            User::create([
                'name' => 'Kasir SMEGABIZ',
                'username' => 'kasir',
                'email' => 'kasir123@gmail.com',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir'
            ]);
        }
    }
}
