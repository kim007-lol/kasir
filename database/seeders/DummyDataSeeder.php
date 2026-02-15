<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 0. Cleanup
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;'); \App\Models\TransactionDetail::truncate(); \App\Models\Transaction::truncate(); \App\Models\StockEntry::truncate(); \App\Models\CashierItem::truncate(); \App\Models\WarehouseItem::truncate(); \App\Models\Member::truncate(); \App\Models\Supplier::truncate(); \App\Models\Category::truncate(); DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = \Faker\Factory::create('id_ID');

        // 1. Categories (Manual List for relevance)
        $categoryNames = [
            'Makanan Ringan',
            'Minuman Dingin',
            'Sembako',
            'Alat Tulis',
            'Perlengkapan Mandi',
            'Bumbu Dapur',
            'Rokok',
            'Obat-obatan',
            'Produk Susu',
            'Kopi & Teh',
            'Roti & Kue',
            'Es Krim'
        ];

        $categories = collect();
        foreach ($categoryNames as $name) {
            $categories->push(\App\Models\Category::create(['name' => $name]));
        }

        // 2. Suppliers (Indonesian Names)
        $supplierNames = [
            'Toko Makmur Jaya',
            'CV Sumber Rejeki',
            'Agen Sembako Murah',
            'UD Maju Bersama',
            'Distributor Wings',
            'Agen Aqua',
            'Toko Kelontong Abadi',
            'PD Sinar Terang'
        ];

        $suppliers = collect();
        foreach ($supplierNames as $name) {
            $suppliers->push(\App\Models\Supplier::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '', $name)) . '@gmail.com',
                'phone' => $faker->phoneNumber,
                'address' => $faker->address
            ]));
        }

        // 3. Members (Indonesian Names)
        $members = \App\Models\Member::factory()->count(30)->create();

        // 4. Warehouse Items (Real Indonesian Products)
        $products = [
            ['name' => 'Indomie Goreng Fried Noodles', 'cat' => 'Makanan Ringan', 'price' => 3500],
            ['name' => 'Indomie Soto Mie', 'cat' => 'Makanan Ringan', 'price' => 3500],
            ['name' => 'Indomie Ayam Bawang', 'cat' => 'Makanan Ringan', 'price' => 3500],
            ['name' => 'Teh Botol Sosro 450ml', 'cat' => 'Minuman Dingin', 'price' => 7000],
            ['name' => 'Aqua Botol 600ml', 'cat' => 'Minuman Dingin', 'price' => 4000],
            ['name' => 'Le Minerale 600ml', 'cat' => 'Minuman Dingin', 'price' => 3500],
            ['name' => 'Beras Pandan Wangi 5kg', 'cat' => 'Sembako', 'price' => 85000],
            ['name' => 'Minyak Goreng Bimoli 2L', 'cat' => 'Sembako', 'price' => 42000],
            ['name' => 'Gula Pasir Gulaku 1kg', 'cat' => 'Sembako', 'price' => 18000],
            ['name' => 'Telur Ayam (1 kg)', 'cat' => 'Sembako', 'price' => 28000],
            ['name' => 'Kopi Kapal Api Special 165g', 'cat' => 'Kopi & Teh', 'price' => 12000],
            ['name' => 'Susu Ultra Coklat 250ml', 'cat' => 'Produk Susu', 'price' => 7500],
            ['name' => 'Roti Tawar Sari Roti', 'cat' => 'Roti & Kue', 'price' => 16000],
            ['name' => 'Beng-Beng Wafer', 'cat' => 'Makanan Ringan', 'price' => 2500],
            ['name' => 'Chitato Sapi Panggang', 'cat' => 'Makanan Ringan', 'price' => 12000],
            ['name' => 'Oreo Original', 'cat' => 'Makanan Ringan', 'price' => 9000],
            ['name' => 'Sabun Lifebuoy Cair 450ml', 'cat' => 'Perlengkapan Mandi', 'price' => 22000],
            ['name' => 'Sampo Sunsilk Hitam 170ml', 'cat' => 'Perlengkapan Mandi', 'price' => 25000],
            ['name' => 'Pepsodent 190g', 'cat' => 'Perlengkapan Mandi', 'price' => 15000],
            ['name' => 'Rokok Sampoerna Mild', 'cat' => 'Rokok', 'price' => 32000],
            ['name' => 'Rokok Gudang Garam Filter', 'cat' => 'Rokok', 'price' => 28000],
            ['name' => 'Buku Tulis Sidu 38 Lembar', 'cat' => 'Alat Tulis', 'price' => 4000],
            ['name' => 'Pulpen Standard AE7', 'cat' => 'Alat Tulis', 'price' => 2500],
            ['name' => 'Royco Ayam Sachet', 'cat' => 'Bumbu Dapur', 'price' => 1000],
            ['name' => 'Kecap Bango 520ml', 'cat' => 'Bumbu Dapur', 'price' => 24000],
        ];

        $warehouseItems = [];
        foreach ($products as $prod) {
            $catId = $categories->where('name', $prod['cat'])->first()->id ?? $categories->first()->id;

            $warehouseItems[] = \App\Models\WarehouseItem::factory()->create([
                'category_id' => $catId,
                'supplier_id' => $suppliers->random()->id,
                'name' => $prod['name'],
                'purchase_price' => $prod['price'] * 0.8,
                'selling_price' => $prod['price'],
                'stock' => $faker->numberBetween(10, 100),
            ]);
        }

        // 5. Cashier Items (Linked)
        foreach ($warehouseItems as $whItem) {
            \App\Models\CashierItem::factory()->create([
                'warehouse_item_id' => $whItem->id,
                'category_id' => $whItem->category_id,
                'supplier_id' => $whItem->supplier_id,
                'code' => $whItem->code,
                'name' => $whItem->name,
                'selling_price' => $whItem->selling_price,
                'discount' => 0,
                'stock' => $whItem->stock,
                'is_consignment' => false,
            ]);
        }

        // 6. Consignment Items (Indonesian Snacks/Kue Basah)
        $consignmentNames = [
            'Lemper Ayam',
            'Risoles Sayur',
            'Pastel Ayam',
            'Tahu Isi',
            'Donat Kentang',
            'Nasi Bungkus',
            'Puding Coklat',
            'Kerupuk Ikan',
            'Pisang Goreng',
            'Bakwan Jagung'
        ];
        $sources = ['Bu Ani', 'Mbak Yul', 'Pak RT', 'Kantin Sebelah', 'Tante Lina'];

        // a. Today's Items
        foreach ($consignmentNames as $index => $name) {
            \App\Models\CashierItem::factory()->create([
                'warehouse_item_id' => null,
                'category_id' => $categories->where('name', 'Makanan Ringan')->first()->id,
                'supplier_id' => null,
                'is_consignment' => true,
                'name' => $name,
                'consignment_source' => $sources[$index % count($sources)] . ' (Hari Ini)',
                'created_at' => now(),
                'updated_at' => now(),
                'selling_price' => 2000 + ($index * 500),
                'cost_price' => 1500 + ($index * 400),
                'stock' => 20,
                'discount' => 0,
            ]);
        }

        // b. Historical Items (Previous Days)
        foreach ($consignmentNames as $index => $name) {
            $daysAgo = $faker->numberBetween(1, 5);
            $date = now()->subDays($daysAgo);

            \App\Models\CashierItem::factory()->create([
                'warehouse_item_id' => null,
                'category_id' => $categories->where('name', 'Makanan Ringan')->first()->id,
                'supplier_id' => null,
                'is_consignment' => true,
                'name' => $name . " (Kemarin)",
                'consignment_source' => $sources[$index % count($sources)] . " ($daysAgo hari lalu)",
                'created_at' => $date,
                'updated_at' => $date,
                'stock' => $faker->numberBetween(0, 5), // Mostly sold out
                'discount' => 0,
            ]);
        }

        // 7. Stock Entries
        for ($i = 0; $i < 30; $i++) {
            $whItem = collect($warehouseItems)->random();
            \App\Models\StockEntry::factory()->create([
                'warehouse_item_id' => $whItem->id,
                'supplier_id' => $whItem->supplier_id,
                'created_at' => now()->subDays($faker->numberBetween(1, 60)),
            ]);
        }

        // 8. Transactions
        $cashierItemsAll = \App\Models\CashierItem::all();
        $user = \App\Models\User::first() ?? \App\Models\User::factory()->create();

        for ($i = 0; $i < 60; $i++) {
            $transactionDate = now()->subDays($faker->numberBetween(0, 60))->setTime($faker->numberBetween(8, 20), $faker->numberBetween(0, 59));

            $transaction = \App\Models\Transaction::factory()->create([
                'user_id' => $user->id,
                'member_id' => $faker->boolean(30) ? $members->random()->id : null,
                'created_at' => $transactionDate,
                'updated_at' => $transactionDate,
                'invoice' => 'TRX-' . $transactionDate->format('ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
            ]);

            $detailsCount = $faker->numberBetween(1, 6);
            $total = 0;

            for ($j = 0; $j < $detailsCount; $j++) {
                $item = $cashierItemsAll->random();
                $qty = $faker->numberBetween(1, 3);
                $subtotal = $item->selling_price * $qty;

                \App\Models\TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $item->id,
                    'price' => $item->selling_price,
                    'original_price' => $item->selling_price + 0,
                    'discount' => 0,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                    'created_at' => $transactionDate,
                    'updated_at' => $transactionDate,
                ]);
                $total += $subtotal;
            }

            $transaction->update([
                'total' => $total,
                'paid_amount' => ceil($total / 50000) * 50000,
                'change_amount' => (ceil($total / 50000) * 50000) - $total,
            ]);
        }
    }
}
