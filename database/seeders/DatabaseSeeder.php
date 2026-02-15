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
        // Call UserSeeder to create admin and kasir users
        $this->call(UserSeeder::class);


        // Create default supplier
        $supplier = Supplier::firstOrCreate(
            ['email' => 'supplier@example.com'],
            [
                'name' => 'Supplier Utama',
                'phone' => '081234567890',
                'address' => 'Jl. Kebahagiaan No. 1'
            ]
        );

        // Create categories
        $kategoriElektronik = Category::firstOrCreate(['name' => 'Elektronik']);
        $kategoriMakanan = Category::firstOrCreate(['name' => 'Makanan']);
        $kategoriMinuman = Category::firstOrCreate(['name' => 'Minuman']);

        // Create items for Elektronik
        WarehouseItem::updateOrCreate(
            ['code' => 'ELE001'],
            [
                'category_id' => $kategoriElektronik->id,
                'supplier_id' => $supplier->id,
                'name' => 'Lampu LED 10W',
                'purchase_price' => 60000,
                'selling_price' => 85000,
                'stock' => 50
            ]
        );

        WarehouseItem::updateOrCreate(
            ['code' => 'ELE002'],
            [
                'category_id' => $kategoriElektronik->id,
                'supplier_id' => $supplier->id,
                'name' => 'Kabel HDMI',
                'purchase_price' => 30000,
                'selling_price' => 45000,
                'stock' => 30
            ]
        );

        // Create items for Makanan
        WarehouseItem::updateOrCreate(
            ['code' => 'MAK001'],
            [
                'category_id' => $kategoriMakanan->id,
                'supplier_id' => $supplier->id,
                'name' => 'Roti Tawar',
                'purchase_price' => 20000,
                'selling_price' => 25000,
                'stock' => 100
            ]
        );

        WarehouseItem::updateOrCreate(
            ['code' => 'MAK002'],
            [
                'category_id' => $kategoriMakanan->id,
                'supplier_id' => $supplier->id,
                'name' => 'Mie Instan',
                'purchase_price' => 2800,
                'selling_price' => 3500,
                'stock' => 200
            ]
        );

        // Create items for Minuman
        WarehouseItem::updateOrCreate(
            ['code' => 'MIN001'],
            [
                'category_id' => $kategoriMinuman->id,
                'supplier_id' => $supplier->id,
                'name' => 'Air Mineral 600ml',
                'purchase_price' => 2500,
                'selling_price' => 4000,
                'stock' => 150
            ]
        );

        WarehouseItem::updateOrCreate(
            ['code' => 'MIN002'],
            [
                'category_id' => $kategoriMinuman->id,
                'supplier_id' => $supplier->id,
                'name' => 'Kopi Instant',
                'purchase_price' => 12000,
                'selling_price' => 15000,
                'stock' => 80
            ]
        );
    }
}
