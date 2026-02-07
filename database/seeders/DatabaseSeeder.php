<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        User::create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create categories
        $kategoriElektronik = Category::create(['name' => 'Elektronik']);
        $kategoriMakanan = Category::create(['name' => 'Makanan']);
        $kategoriMinuman = Category::create(['name' => 'Minuman']);

        // Create items for Elektronik
        Item::create([
            'category_id' => $kategoriElektronik->id,
            'code' => 'ELE001',
            'name' => 'Lampu LED 10W',
            'price' => 85000,
            'stock' => 50
        ]);

        Item::create([
            'category_id' => $kategoriElektronik->id,
            'code' => 'ELE002',
            'name' => 'Kabel HDMI',
            'price' => 45000,
            'stock' => 30
        ]);

        // Create items for Makanan
        Item::create([
            'category_id' => $kategoriMakanan->id,
            'code' => 'MAK001',
            'name' => 'Roti Tawar',
            'price' => 25000,
            'stock' => 100
        ]);

        Item::create([
            'category_id' => $kategoriMakanan->id,
            'code' => 'MAK002',
            'name' => 'Mie Instan',
            'price' => 3500,
            'stock' => 200
        ]);

        // Create items for Minuman
        Item::create([
            'category_id' => $kategoriMinuman->id,
            'code' => 'MIN001',
            'name' => 'Air Mineral 600ml',
            'price' => 4000,
            'stock' => 150
        ]);

        Item::create([
            'category_id' => $kategoriMinuman->id,
            'code' => 'MIN002',
            'name' => 'Kopi Instant',
            'price' => 15000,
            'stock' => 80
        ]);
    }
}
