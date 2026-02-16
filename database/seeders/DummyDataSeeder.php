<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Member;
use App\Models\WarehouseItem;
use App\Models\CashierItem;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\StockTransferLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Categories (Minimal 20)
        $categoryNames = [
            'Sembako',
            'Minuman Ringan',
            'Makanan Ringan',
            'Kebutuhan Mandi',
            'Pembersih Rumah',
            'Obat-obatan',
            'Alat Tulis Kantor',
            'Bumbu Dapur',
            'Susu & Nutrisi',
            'Roti & Kue',
            'Mie & Pasta',
            'Minyak & Lemak',
            'Perawatan Tubuh',
            'Perawatan Rambut',
            'Kesehatan Mulut',
            'Tisue & Kapas',
            'Perlengkapan Bayi',
            'Makanan Kaleng',
            'Snack Tradisional',
            'Minuman Kesehatan'
        ];

        foreach ($categoryNames as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
        $categories = Category::all();

        // 2. Suppliers (Minimal 20)
        for ($i = 0; $i < 20; $i++) {
            Supplier::firstOrCreate(
                ['email' => $faker->unique()->companyEmail],
                [
                    'name' => 'CV. ' . $faker->company,
                    'phone' => $faker->phoneNumber,
                    'address' => $faker->address,
                ]
            );
        }
        $suppliers = Supplier::all();

        // 3. Members (Minimal 20)
        for ($i = 0; $i < 25; $i++) {
            $phone = $faker->unique()->phoneNumber;
            Member::firstOrCreate(
                ['phone' => $phone],
                [
                    'name' => $faker->name,
                    'address' => $faker->address,
                ]
            );
        }
        $members = Member::all();

        // 4. Items (Minimal 20)
        $productNames = [
            'Beras Maknyus 5kg',
            'Minyak Goreng SunCo 2L',
            'Gula Gulaku 1kg',
            'Garam Cap Kapal 250g',
            'Aqua 600ml',
            'Teh Botol Sosro 450ml',
            'Indomie Goreng',
            'Mie Sedap Soto',
            'Susu Ultra 1L',
            'Kopi Kapal Api 165g',
            'Sabun Lifebuoy 100g',
            'Pasta Gigi Pepsodent',
            'Shampoo Sunsilk 170ml',
            'Tisue Paseo 250s',
            'Roti Tawar Sari Roti',
            'Kecap Bango 550ml',
            'Saus ABC 335ml',
            'Tepung Segitiga Biru 1kg',
            'Telur Ayam 1kg',
            'Beng-Beng 20g'
        ];

        foreach ($productNames as $index => $name) {
            $cat = $categories->random();
            $sup = $suppliers->random();
            $purchasePrice = $faker->numberBetween(1000, 50000);
            $sellingPrice = $purchasePrice * 1.25;
            $code = 'BRG-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);

            $whItem = WarehouseItem::updateOrCreate(
                ['code' => $code],
                [
                    'category_id' => $cat->id,
                    'supplier_id' => $sup->id,
                    'name' => $name,
                    'purchase_price' => $purchasePrice,
                    'selling_price' => $sellingPrice,
                    'stock' => $faker->numberBetween(50, 200),
                ]
            );

            // M5 Fix: Transfer stok dari gudang ke kasir, bukan copy
            $transferQty = min($whItem->stock, $faker->numberBetween(20, 80));

            CashierItem::updateOrCreate(
                ['warehouse_item_id' => $whItem->id],
                [
                    'category_id' => $cat->id,
                    'supplier_id' => $sup->id,
                    'code' => $whItem->code,
                    'name' => $whItem->name,
                    'selling_price' => $whItem->selling_price,
                    'stock' => $transferQty,
                    'is_consignment' => false,
                    'cost_price' => $whItem->purchase_price,
                    'discount' => 0,
                ]
            );

            // Kurangi stok gudang sesuai jumlah transfer
            $whItem->decrement('stock', $transferQty);

            // Buat log transfer stok
            StockTransferLog::create([
                'warehouse_item_id' => $whItem->id,
                'cashier_item_id' => CashierItem::where('warehouse_item_id', $whItem->id)->first()->id,
                'type' => 'warehouse_to_cashier',
                'quantity' => $transferQty,
                'user_id' => User::first()->id,
                'notes' => 'Seeder: Transfer awal ke kasir',
            ]);
        }
        $items = CashierItem::all();

        // 5. Transactions (Minimal 20)
        $user = User::first();
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays(rand(0, 30));
            $invoice = 'TRX-' . $date->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);

            if (Transaction::where('invoice', $invoice)->exists()) continue;

            $transaction = Transaction::create([
                'invoice' => $invoice,
                'user_id' => $user->id,
                'member_id' => $faker->boolean(40) ? $members->random()->id : null,
                'customer_name' => $faker->name,
                'payment_method' => $faker->randomElement(['cash', 'qris']),
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => 0,
                'paid_amount' => 0,
                'change_amount' => 0,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            $total = 0;
            $detailsCount = rand(1, 5);
            for ($j = 0; $j < $detailsCount; $j++) {
                $item = $items->random();
                $qty = rand(1, 4);
                $subtotal = $item->selling_price * $qty;

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $item->id,
                    'price' => $item->selling_price,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                    'discount' => 0,
                    'original_price' => $item->selling_price,
                    'purchase_price' => $item->cost_price ?: ($item->selling_price * 0.8),
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
                $total += $subtotal;
            }

            $paid = ceil($total / 5000) * 5000;
            if ($transaction->payment_method != 'cash') $paid = $total;

            $transaction->update([
                'total' => $total,
                'paid_amount' => $paid,
                'change_amount' => $paid - $total,
            ]);
        }
    }
}
