<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Member;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WarehouseItem;
use App\Models\CashierItem;
use App\Models\StockEntry;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Booking;
use App\Models\BookingItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DemoSeeder extends Seeder
{
    /**
     * Seed demo data — SEMUA DATA DI SINI FAKE.
     * Tidak mengganggu data real apapun.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // ================================================================
        // 0. CLEANUP — Hapus data demo sebelumnya agar bisa re-run
        //    Hanya menghapus data dengan prefix 'D' (demo), data real AMAN.
        // ================================================================
        $this->command?->info('🧹 Membersihkan data demo lama...');

        // Hapus booking items & bookings milik demo user
        $demoUserIds = User::where('username', 'like', 'demo_%')->pluck('id');
        $demoBookingIds = Booking::whereIn('user_id', $demoUserIds)->pluck('id');
        BookingItem::whereIn('booking_id', $demoBookingIds)->delete();
        Booking::whereIn('id', $demoBookingIds)->delete();

        // Hapus transaksi demo (invoice diawali DINV-)
        $demoTxIds = Transaction::where('invoice', 'like', 'DINV-%')->pluck('id');
        TransactionDetail::whereIn('transaction_id', $demoTxIds)->delete();
        Transaction::whereIn('id', $demoTxIds)->delete();

        // Hapus cashier items demo (code diawali DC- atau DCONS-)
        CashierItem::where('code', 'like', 'DC-%')
            ->orWhere('code', 'like', 'DCONS-%')
            ->forceDelete();

        // Hapus stock entries & warehouse items demo (code diawali DW-)
        $demoWIds = WarehouseItem::where('code', 'like', 'DW-%')->pluck('id');
        StockEntry::whereIn('warehouse_item_id', $demoWIds)->delete();
        WarehouseItem::whereIn('id', $demoWIds)->forceDelete();

        // Hapus members tanpa user_id yang dibuat demo (simpan member real)
        // Kita tidak hapus member karena bisa bentrok — biarkan saja bertambah

        $this->command?->info('✅ Cleanup selesai.');

        // ================================================================
        // 1. AKUN DEMO (3 role: admin, kasir, pelanggan)
        // ================================================================
        $demoUsers = [
            [
                'name'     => 'Demo Admin',
                'username' => 'demo_admin',
                'email'    => 'admin@demo.smegamart.id',
                'role'     => 'admin',
                'password' => Hash::make('demoadmin123'),
            ],
            [
                'name'     => 'Demo Kasir',
                'username' => 'demo_kasir',
                'email'    => 'kasir@demo.smegamart.id',
                'role'     => 'kasir',
                'password' => Hash::make('demokasir123'),
            ],
            [
                'name'     => 'Demo Pelanggan',
                'username' => 'demo_pelanggan',
                'email'    => 'pelanggan@demo.smegamart.id',
                'role'     => 'pelanggan',
                'password' => Hash::make('demopelanggan123'),
            ],
        ];

        foreach ($demoUsers as $userData) {
            // SEC: 'role' is NOT in $fillable — must be set explicitly
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::firstOrCreate(
                ['username' => $userData['username']],
                $userData
            );
            $user->role = $role;
            $user->save();

            // Buat member profile untuk pelanggan
            if ($user->role === 'pelanggan') {
                Member::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name'    => $user->name,
                        'phone'   => '0812' . $faker->randomNumber(8, true),
                        'address' => 'Jl. Demo Pelanggan No. 1',
                    ]
                );
            }
        }

        $demoAdmin     = User::where('username', '=', 'demo_admin')->first();
        $demoKasir     = User::where('username', '=', 'demo_kasir')->first();
        $demoPelanggan = User::where('username', '=', 'demo_pelanggan')->first();

        // ================================================================
        // 2. KATEGORI (10 kategori)
        // ================================================================
        $categoryNames = [
            'Makanan Berat', 'Minuman Dingin', 'Snack & Camilan', 'Bahan Pokok',
            'Sayuran Segar', 'Daging & Ikan', 'Pembersih', 'Kebutuhan Mandi',
            'Alat Tulis', 'Obat-obatan',
        ];

        $categoryIds = [];
        foreach ($categoryNames as $catName) {
            $c = Category::firstOrCreate(['name' => $catName]);
            $categoryIds[] = $c->id;
        }

        // ================================================================
        // 3. SUPPLIER (8 supplier)
        //    fillable: name, phone, email, address, contract_date
        // ================================================================
        $supplierIds = [];
        $supplierNames = [
            'PT. Sumber Makmur', 'CV. Pangan Nusantara', 'UD. Sejahtera Abadi',
            'PT. Berkah Jaya', 'CV. Maju Bersama', 'PT. Indofood Supply',
            'UD. Sinar Pagi', 'CV. Tekun Mandiri',
        ];
        foreach ($supplierNames as $sName) {
            $s = Supplier::firstOrCreate(
                ['name' => $sName],
                [
                    'phone'         => '021' . $faker->randomNumber(8, true),
                    'email'         => $faker->unique()->safeEmail,
                    'address'       => $faker->address,
                    'contract_date' => Carbon::now()->subMonths($faker->numberBetween(1, 12)),
                ]
            );
            $supplierIds[] = $s->id;
        }

        // ================================================================
        // 4. WAREHOUSE ITEMS (60 items)
        //    fillable: category_id, supplier_id, code, name, purchase_price,
        //              selling_price, discount, stock, exp_date
        // ================================================================
        $warehouseProducts = [
            'Nasi Goreng Instan', 'Mie Goreng Sedaap', 'Indomie Kuah Soto', 'Kopi Kapal Api',
            'Teh Celup Sariwangi', 'Susu Ultra Milk', 'Roti Tawar Sari Roti', 'Beras Premium 5kg',
            'Gula Pasir 1kg', 'Minyak Goreng 2L', 'Telur Ayam 1kg', 'Sabun Cuci Rinso',
            'Sabun Mandi Lifebuoy', 'Shampo Pantene', 'Pasta Gigi Pepsodent',
            'Tissue Paseo 250', 'Air Mineral Aqua 600ml', 'Fanta Strawberry 390ml',
            'Coca Cola 390ml', 'Sprite 390ml', 'Pocari Sweat 500ml', 'Yakult 5pcs',
            'Sarden ABC', 'Kecap Manis ABC', 'Saos Sambal ABC', 'Bumbu Racik Indofood',
            'Keju Kraft Singles', 'Mentega Blue Band', 'Tepung Terigu 1kg', 'Tepung Beras 500g',
            'Minyak Wijen 150ml', 'Saus Tiram 135ml', 'Krupuk Udang', 'Chitato 68g',
            'Lays Classic 65g', 'Oreo 137g', 'Tango Wafer 176g', 'Roma Kelapa 300g',
            'Good Day Cappuccino', 'Nescafe Sachet', 'Pop Mie Ayam', 'SEDAAP Cup Soto',
            'Beng Beng 32g', 'KitKat 2F', 'Silverqueen 65g', 'Nutella 200g',
            'Ovaltine Sachet', 'Bear Brand Gold', 'Frisian Flag Coklat', 'Ultra Mimi Coklat',
            'Dettol Antiseptik', 'Baygon Aerosol', 'Hit Obat Nyamuk', 'SOS Pembersih Lantai',
            'Molto Pewangi', 'Downy Pelembut', 'Pulpen Pilot', 'Buku Tulis Sinar Dunia',
            'Pensil 2B Faber', 'Penghapus Staedtler',
        ];

        $warehouseItemIds = [];
        foreach ($warehouseProducts as $idx => $productName) {
            $purchasePrice = $faker->numberBetween(3, 80) * 1000;
            $sellingPrice  = $purchasePrice + ($faker->numberBetween(1, 15) * 1000);

            $w = WarehouseItem::create([
                'category_id'    => $faker->randomElement($categoryIds),
                'supplier_id'    => $faker->randomElement($supplierIds),
                'code'           => 'DW-' . str_pad($idx, 4, '0', STR_PAD_LEFT),
                'name'           => $productName,
                'purchase_price' => $purchasePrice,
                'selling_price'  => $sellingPrice,
                'discount'       => 0,
                'stock'          => $faker->numberBetween(10, 200),
                'exp_date'       => Carbon::now()->addDays($faker->numberBetween(30, 365)),
            ]);
            $warehouseItemIds[] = $w->id;

            // Riwayat masuk gudang
            StockEntry::create([
                'warehouse_item_id' => $w->id,
                'supplier_id'       => $w->supplier_id,
                'quantity'          => $w->stock,
                'entry_date'        => Carbon::now()->subDays($faker->numberBetween(1, 30)),
            ]);
        }

        // ================================================================
        // 5. CASHIER ITEMS (40 dari warehouse + 10 titipan = 50)
        //    fillable: warehouse_item_id, category_id, supplier_id, code, name,
        //              selling_price, cost_price, discount, stock, expiry_date,
        //              is_consignment, consignment_source
        // ================================================================
        $cashierItemIds = [];

        // Ambil 40 warehouse items ke kasir
        $selectedWarehouse = WarehouseItem::whereIn('id', $warehouseItemIds)
            ->inRandomOrder()
            ->take(40)
            ->get();

        foreach ($selectedWarehouse as $wItem) {
            $c = CashierItem::create([
                'warehouse_item_id' => $wItem->id,
                'category_id'       => $wItem->category_id,
                'supplier_id'       => $wItem->supplier_id,
                'code'              => 'DC-' . $wItem->code,
                'name'              => $wItem->name,
                'selling_price'     => $wItem->selling_price,
                'cost_price'        => $wItem->purchase_price,
                'discount'          => $faker->randomElement([0, 0, 0, 1000, 2000]),
                'stock'             => $faker->numberBetween(5, 50),
                'expiry_date'       => $wItem->exp_date,
                'is_consignment'    => false,
            ]);
            $cashierItemIds[] = $c->id;
        }

        // 10 barang titipan (consignment)
        $consignmentNames = [
            'Kue Lapis Legit', 'Donat Kentang', 'Risol Mayo', 'Pastel Goreng',
            'Lemper Ayam', 'Onde-onde', 'Klepon Isi Gula', 'Pisang Goreng',
            'Tahu Isi Pedas', 'Bakwan Sayur',
        ];
        foreach ($consignmentNames as $cidx => $cName) {
            $sp = $faker->numberBetween(3, 15) * 1000;
            $c = CashierItem::create([
                'code'               => 'DCONS-' . str_pad($cidx, 3, '0', STR_PAD_LEFT),
                'name'               => $cName,
                'cost_price'         => $sp - 2000,
                'selling_price'      => $sp,
                'discount'           => 0,
                'stock'              => $faker->numberBetween(5, 20),
                'is_consignment'     => true,
                'consignment_source' => $faker->randomElement($supplierNames),
            ]);
            $cashierItemIds[] = $c->id;
        }

        // ================================================================
        // 6. MEMBERS (30 member dummy — SEMUA terhubung ke demo user)
        //    fillable: name, phone, address, user_id
        //    SECURITY: Member scope hanya menampilkan member dengan
        //    user_id milik demo user. Jadi semua member dummy harus
        //    di-link ke salah satu demo user.
        // ================================================================
        $members = [];
        for ($i = 0; $i < 30; $i++) {
            $m = Member::create([
                'name'    => $faker->name,
                'phone'   => '0812' . $faker->randomNumber(8, true),
                'address' => $faker->address,
                'user_id' => $demoPelanggan->id,
            ]);
            $members[] = $m;
        }

        // Tambahkan member pelanggan demo
        $memberPelanggan = Member::where('user_id', '=', $demoPelanggan->id)->first();
        if ($memberPelanggan) {
            $members[] = $memberPelanggan;
        }

        // ================================================================
        // 7. TRANSAKSI POS (60 transaksi)
        //    Transaction fillable: invoice, customer_name, total, user_id,
        //      paid_amount, change_amount, payment_method, member_id,
        //      discount_percent, discount_amount, cashier_name, source, booking_id
        //    TransactionDetail fillable: transaction_id, item_id, price,
        //      original_price, discount, qty, subtotal, purchase_price
        // ================================================================
        $allCashierItems = CashierItem::whereIn('id', $cashierItemIds)->get();

        for ($i = 0; $i < 60; $i++) {
            $txDate   = Carbon::now()->subDays($faker->numberBetween(0, 30))
                            ->setTime($faker->numberBetween(8, 20), $faker->numberBetween(0, 59));
            $isQris   = $faker->boolean(40);
            $member   = $faker->boolean(60) ? $faker->randomElement($members) : null;

            // Use new() + save() instead of create() because
            // total, change_amount, discount_* are NOT in $fillable but are NOT NULL in DB
            $discountAmt = $faker->randomElement([0, 0, 0, 5000, 10000]);
            $transaction = new Transaction([
                'invoice'          => 'DINV-' . $txDate->format('YmdHis') . str_pad($i, 3, '0', STR_PAD_LEFT),
                'customer_name'    => $member ? $member->name : 'Non Member',
                'user_id'          => $faker->randomElement([$demoAdmin->id, $demoKasir->id]),
                'paid_amount'      => 0,
                'payment_method'   => $isQris ? 'qris' : 'cash',
                'member_id'        => $member?->id,
                'cashier_name'     => 'Demo Kasir',
                'source'           => 'pos',
                'created_at'       => $txDate,
                'updated_at'       => $txDate,
            ]);
            $transaction->total = 0;
            $transaction->change_amount = 0;
            $transaction->discount_percent = 0;
            $transaction->discount_amount = $discountAmt;
            $transaction->save();

            $total      = 0;
            $itemsCount = $faker->numberBetween(1, 5);
            $selected   = $allCashierItems->random(min($itemsCount, $allCashierItems->count()));

            foreach ($selected as $cItem) {
                $qty      = $faker->numberBetween(1, 3);
                $subtotal = $cItem->final_price * $qty;
                $total   += $subtotal;

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'item_id'        => $cItem->id,
                    'price'          => $cItem->final_price,
                    'original_price' => $cItem->selling_price,
                    'discount'       => $cItem->discount,
                    'qty'            => $qty,
                    'subtotal'       => $subtotal,
                    'purchase_price' => $cItem->cost_price ?? 0,
                ]);
            }

            $netTotal = max(0, $total - $discountAmt);
            $paid     = $isQris ? $netTotal : (ceil($netTotal / 5000) * 5000);

            $transaction->total = $netTotal;
            $transaction->paid_amount = $paid;
            $transaction->change_amount = max(0, $paid - $netTotal);
            $transaction->save();
        }

        // ================================================================
        // 8. BOOKING ONLINE (30 pesanan)
        //    Booking fillable: booking_code, user_id, customer_name,
        //      customer_phone, delivery_type, pickup_time, delivery_address,
        //      status, total, notes, payment_method, amount_paid, cancel_reason
        //    BookingItem fillable: booking_id, cashier_item_id, name,
        //      qty, price, subtotal, notes
        // ================================================================
        $bookingStatuses = ['pending', 'confirmed', 'processing', 'ready', 'completed', 'cancelled'];

        for ($i = 0; $i < 30; $i++) {
            $bDate  = Carbon::now()->subDays($faker->numberBetween(0, 15))
                           ->setTime($faker->numberBetween(8, 20), $faker->numberBetween(0, 59));
            $status = $faker->randomElement($bookingStatuses);
            $deliveryType    = $faker->randomElement(['pickup', 'delivery']);

            // Use new() + save() instead of create() because
            // status and total are NOT in $fillable but are NOT NULL in DB
            $booking = new Booking([
                'booking_code'     => Booking::generateBookingCode(),
                'user_id'          => $demoPelanggan->id,
                'customer_name'    => $faker->name,
                'customer_phone'   => '0812' . $faker->randomNumber(8, true),
                'delivery_type'    => $deliveryType,
                'pickup_time'      => $bDate->copy()->addMinutes($faker->numberBetween(30, 120)),
                'delivery_address' => $deliveryType === 'delivery' ? $faker->address : null,
                'notes'            => $faker->randomElement(['', 'Tidak pakai pedas', 'Extra sambal', 'Pakai kantong']),
                'payment_method'   => $faker->randomElement(['cash', 'qris']),
                'amount_paid'      => 0,
                'cancel_reason'    => $status === 'cancelled' ? 'Customer membatalkan pesanan' : null,
                'created_at'       => $bDate,
                'updated_at'       => $bDate,
            ]);
            $booking->status = $status;
            $booking->total = 0;
            $booking->save();

            $total      = 0;
            $itemsCount = $faker->numberBetween(1, 4);
            $selected   = $allCashierItems->random(min($itemsCount, $allCashierItems->count()));

            foreach ($selected as $cItem) {
                $qty      = $faker->numberBetween(1, 3);
                $subtotal = $cItem->final_price * $qty;
                $total   += $subtotal;

                BookingItem::create([
                    'booking_id'     => $booking->id,
                    'cashier_item_id' => $cItem->id,
                    'name'           => $cItem->name,
                    'qty'            => $qty,
                    'price'          => $cItem->final_price,
                    'subtotal'       => $subtotal,
                    'notes'          => '',
                ]);
            }

            $booking->total = $total;
            $booking->amount_paid = in_array($status, ['completed', 'ready']) ? $total : 0;
            $booking->save();
        }

        $this->command?->info('✅ Demo data seeded successfully! (3 akun, 60 warehouse items, 50 cashier items, 30 members, 60 transaksi, 30 booking)');
    }
}
