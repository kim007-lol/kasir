<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        \App\Models\User::updateOrCreate(['email' => 'admin@test.com'], [
            'name' => 'Admin Test',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
        ]);

        // Cashier
        \App\Models\User::updateOrCreate(['email' => 'cashier@test.com'], [
            'name' => 'Cashier Test',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'kasir',
        ]);

        // Customer
        $customerUser = \App\Models\User::updateOrCreate(['email' => 'member@test.com'], [
            'name' => 'Customer Test',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'pelanggan',
        ]);

        \App\Models\Member::updateOrCreate(['user_id' => $customerUser->id], [
            'name' => 'Customer Test',
            'phone' => '08123456789',
            'address' => 'Test Address',
            'points' => 0,
        ]);

        // Category
        $category = \App\Models\Category::updateOrCreate(['name' => 'Test Category'], [
            'description' => 'Test',
        ]);

        $supplier = \App\Models\Supplier::updateOrCreate(['name' => 'Test Supplier'], [
            'phone' => '08123',
            'address' => 'Test'
        ]);

        $warehouseItem = \App\Models\WarehouseItem::updateOrCreate(['name' => 'Test Item'], [
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'purchase_price' => 5000,
            'stock' => 100,
            'unit' => 'pcs',
        ]);

        // Item
        \App\Models\CashierItem::updateOrCreate(['warehouse_item_id' => $warehouseItem->id], [
            'name' => 'Test Item',
            'category_id' => $category->id,
            'purchase_price' => 5000,
            'price' => 10000,
            'stock' => 100,
            'barcode' => 'TEST001',
            'is_consignment' => false,
        ]);
    }
}
