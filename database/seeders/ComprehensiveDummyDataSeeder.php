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
use App\Models\User;
use App\Models\StockTransferLog;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Carbon\Carbon;

class ComprehensiveDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Categories
        $categories = [
            'Sembako & Bahan Pokok' => '📦',
            'Minuman & Soda' => '🥤',
            'Makanan Ringan' => '🥨',
            'Susu & Produk Olahan' => '🥛',
            'Bumbu Dapur' => '🧂',
            'Mie Instant' => '🍜',
            'Kebutuhan Mandi' => '🧼',
            'Perawatan Rambut' => '💇',
            'Kesehatan Mulut' => '🪥',
            'Pembersih Rumah' => '🧹',
            'Obat-obatan' => '💊',
            'Tisue & Kapas' => '🧻',
            'Perlengkapan Bayi' => '👶',
            'Alat Tulis Kantor' => '📝',
            'Roti & Selai' => '🍞',
            'Makanan Kaleng' => '🥫',
            'Minyak & Lemak' => '🛢️',
            'Snack Tradisional' => '🍪',
            'Minuman Kesehatan' => '🍵',
            'Perlengkapan Dapur' => '🍳'
        ];

        $categoryModels = [];
        foreach ($categories as $name => $icon) {
            $categoryModels[] = Category::firstOrCreate(['name' => $name]);
        }

        // 2. Suppliers
        $supplierNames = [
            'PT. Sumber Makmur Sejahtera',
            'CV. Maju Jaya Abadi',
            'Distribusi Nasional Food',
            'PT. Global Niaga Utama',
            'Grosir Sembako Kita',
            'PT. Indofood Sukses Makmur',
            'Wings Surya Distributor',
            'PT. Unilever Indonesia Tbk',
            'Mayora Distribusi',
            'PT. Santos Jaya Abadi'
        ];

        $supplierModels = [];
        foreach ($supplierNames as $name) {
            $supplierModels[] = Supplier::firstOrCreate(
                ['name' => $name],
                [
                    'phone' => '08' . $faker->numerify('##########'),
                    'address' => $faker->address,
                    'email' => strtolower(str_replace([' ', '.'], '', $name)) . '@supplier.com',
                    'contract_date' => Carbon::now()->subMonths(rand(1, 24))
                ]
            );
        }

        // 3. Members
        for ($i = 0; $i < 20; $i++) {
            Member::firstOrCreate(
                ['phone' => '08' . $faker->unique()->numerify('##########')],
                [
                    'name' => $faker->name,
                    'address' => $faker->address,
                ]
            );
        }
        $allMembers = Member::all();

        // 4. Products (Warehouse & Cashier)
        $products = [
            ['name' => 'Beras Pandan Wangi 5kg', 'cat' => 'Sembako & Bahan Pokok', 'price' => 75000],
            ['name' => 'Minyak Goreng Bimoli 2L', 'cat' => 'Minyak & Lemak', 'price' => 38000],
            ['name' => 'Gula Pasir Gulaku 1kg', 'cat' => 'Sembako & Bahan Pokok', 'price' => 16500],
            ['name' => 'Indomie Goreng Original', 'cat' => 'Mie Instant', 'price' => 3100],
            ['name' => 'Susu Kental Manis Frisian Flag', 'cat' => 'Susu & Produk Olahan', 'price' => 12500],
            ['name' => 'Teh Celup Sariwangi 25s', 'cat' => 'Minuman & Soda', 'price' => 8500],
            ['name' => 'Kopi Kapal Api Spesial 165g', 'cat' => 'Minuman & Soda', 'price' => 14500],
            ['name' => 'Garam Cap Kapal 250g', 'cat' => 'Bumbu Dapur', 'price' => 2500],
            ['name' => 'Kecap Bango Pedas 550ml', 'cat' => 'Bumbu Dapur', 'price' => 22000],
            ['name' => 'Sabun Mandi Lifebuoy Red', 'cat' => 'Kebutuhan Mandi', 'price' => 4500],
            ['name' => 'Sikat Gigi Pepsodent Soft', 'cat' => 'Kesehatan Mulut', 'price' => 9500],
            ['name' => 'Shampoo Sunsilk Black Silky', 'cat' => 'Perawatan Rambut', 'price' => 24000],
            ['name' => 'Pasta Gigi Pepsodent 190g', 'cat' => 'Kesehatan Mulut', 'price' => 15500],
            ['name' => 'Tisue Paseo 250 Sheets', 'cat' => 'Tisue & Kapas', 'price' => 13500],
            ['name' => 'Deterjen Rinso Molto 770g', 'cat' => 'Pembersih Rumah', 'price' => 29000],
            ['name' => 'Pembersih Lantai So Klin 800ml', 'cat' => 'Pembersih Rumah', 'price' => 11000],
            ['name' => 'Roti Tawar Sari Roti', 'cat' => 'Roti & Selai', 'price' => 15000],
            ['name' => 'Sarden ABC Tomat 155g', 'cat' => 'Makanan Kaleng', 'price' => 11500],
            ['name' => 'Minyak Kayu Putih Cap Lang 60ml', 'cat' => 'Obat-obatan', 'price' => 26000],
            ['name' => 'Aqua Air Mineral 600ml', 'cat' => 'Minuman & Soda', 'price' => 3500]
        ];

        $user = User::first();

        foreach ($products as $p) {
            $cat = Category::where('name', $p['cat'])->first();
            $sup = $faker->randomElement($supplierModels);
            $purchasePrice = $p['price'] * 0.85;
            $code = 'PRD-' . strtoupper($faker->unique()->bothify('###??'));

            $whItem = WarehouseItem::updateOrCreate(
                ['code' => $code],
                [
                    'category_id' => $cat->id,
                    'supplier_id' => $sup->id,
                    'name' => $p['name'],
                    'purchase_price' => $purchasePrice,
                    'selling_price' => $p['price'],
                    'stock' => $faker->numberBetween(100, 500),
                ]
            );

            $transferQty = $faker->numberBetween(20, 50);
            $whItem->decrement('stock', $transferQty);

            $cashierItem = CashierItem::updateOrCreate(
                ['warehouse_item_id' => $whItem->id],
                [
                    'category_id' => $cat->id,
                    'supplier_id' => $sup->id,
                    'code' => $whItem->code,
                    'name' => $whItem->name,
                    'selling_price' => $whItem->selling_price,
                    'stock' => $transferQty,
                    'cost_price' => $whItem->purchase_price,
                    'discount' => $faker->boolean(20) ? ($p['price'] * 0.1) : 0,
                    'is_consignment' => false,
                ]
            );

            StockTransferLog::create([
                'warehouse_item_id' => $whItem->id,
                'cashier_item_id' => $cashierItem->id,
                'type' => 'transfer_in',
                'quantity' => $transferQty,
                'user_id' => $user->id,
                'notes' => 'Stok awal dummy',
                'item_name' => $whItem->name,
                'item_code' => $whItem->code,
            ]);
        }

        // 5. Transactions
        $allCashierItems = CashierItem::all();
        for ($i = 0; $i < 50; $i++) {
            $date = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 12))->subMinutes(rand(0, 59));
            $invoice = 'INV-' . $date->format('YmdHis') . '-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT) . '-' . strtoupper($faker->bothify('??'));
            
            try {
                $transaction = Transaction::create([
                    'invoice' => $invoice,
                    'user_id' => $user->id,
                    'member_id' => $faker->boolean(50) ? $allMembers->random()->id : null,
                    'customer_name' => $faker->name,
                    'payment_method' => $faker->randomElement(['cash', 'qris']),
                    'total' => 0,
                    'paid_amount' => 0,
                    'change_amount' => 0,
                    'discount_percent' => 0,
                    'discount_amount' => 0,
                    'cashier_name' => $user->name,
                    'source' => 'pos',
                    'created_at' => $date,
                ]);

                $total = 0;
                $itemsCount = rand(1, 6);
                $randomItems = $allCashierItems->random($itemsCount);

                foreach ($randomItems as $item) {
                    $qty = rand(1, 3);
                    $subtotal = $item->final_price * $qty;

                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'item_id' => $item->id,
                        'price' => $item->selling_price,
                        'original_price' => $item->selling_price,
                        'discount' => $item->discount,
                        'qty' => $qty,
                        'subtotal' => $subtotal,
                        'purchase_price' => $item->cost_price ?: ($item->selling_price * 0.8),
                        'created_at' => $date,
                    ]);
                    $total += $subtotal;
                }

                $paid = ceil($total / 1000) * 1000;
                if ($transaction->payment_method != 'cash') $paid = $total;

                $transaction->update([
                    'total' => $total,
                    'paid_amount' => $paid,
                    'change_amount' => $paid - $total,
                ]);
            } catch (\Exception $e) {
                echo "FAILED INVOICE: $invoice\n";
                throw $e;
            }
        }
    }
}
