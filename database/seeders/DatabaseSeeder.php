<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\WarehouseItem;
use App\Models\Supplier;
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
            'name' => 'Ngabdullah Hakim',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create default supplier
        $supplier = Supplier::create([
            'name' => 'Supplier Utama',
            'email' => 'supplier@example.com',
            'phone' => '081234567890',
            'address' => 'Jl. Kebahagiaan No. 1'
        ]);

        // Create categories
        $kategoriElektronik = Category::create(['name' => 'Elektronik']);
        $kategoriMakanan = Category::create(['name' => 'Makanan']);
        $kategoriMinuman = Category::create(['name' => 'Minuman']);

        // Create items for Elektronik
        WarehouseItem::create([
            'category_id' => $kategoriElektronik->id,
            'supplier_id' => $supplier->id,
            'code' => 'ELE001',
            'name' => 'Lampu LED 10W',
            'purchase_price' => 60000,
            'selling_price' => 85000,
            'stock' => 50
        ]);

        WarehouseItem::create([
            'category_id' => $kategoriElektronik->id,
            'supplier_id' => $supplier->id,
            'code' => 'ELE002',
            'name' => 'Kabel HDMI',
            'purchase_price' => 30000,
            'selling_price' => 45000,
            'stock' => 30
        ]);

        // Create items for Makanan
        WarehouseItem::create([
            'category_id' => $kategoriMakanan->id,
            'supplier_id' => $supplier->id,
            'code' => 'MAK001',
            'name' => 'Roti Tawar',
            'purchase_price' => 20000,
            'selling_price' => 25000,
            'stock' => 100
        ]);

        WarehouseItem::create([
            'category_id' => $kategoriMakanan->id,
            'supplier_id' => $supplier->id,
            'code' => 'MAK002',
            'name' => 'Mie Instan',
            'purchase_price' => 2800,
            'selling_price' => 3500,
            'stock' => 200
        ]);

        // Create items for Minuman
        WarehouseItem::create([
            'category_id' => $kategoriMinuman->id,
            'supplier_id' => $supplier->id,
            'code' => 'MIN001',
            'name' => 'Air Mineral 600ml',
            'purchase_price' => 2500,
            'selling_price' => 4000,
            'stock' => 150
        ]);

        WarehouseItem::create([
            'category_id' => $kategoriMinuman->id,
            'supplier_id' => $supplier->id,
            'code' => 'MIN002',
            'name' => 'Kopi Instant',
            'purchase_price' => 12000,
            'selling_price' => 15000,
            'stock' => 80
        ]);
    }
}
