<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Categories
        $categories = \App\Models\Category::factory()->count(20)->create();

        // 2. Suppliers
        $suppliers = \App\Models\Supplier::factory()->count(20)->create();

        // 3. Members
        \App\Models\Member::factory()->count(20)->create();

        // 4. Warehouse Items
        $warehouseItems = [];
        for ($i = 0; $i < 20; $i++) {
            $warehouseItems[] = \App\Models\WarehouseItem::factory()->create([
                'category_id' => $categories->random()->id,
                'supplier_id' => $suppliers->random()->id,
            ]);
        }

        // 5. Cashier Items
        foreach ($warehouseItems as $whItem) {
            \App\Models\CashierItem::factory()->create([
                'warehouse_item_id' => $whItem->id,
                'category_id' => $whItem->category_id,
                'supplier_id' => $whItem->supplier_id,
                'code' => $whItem->code,
                'name' => $whItem->name,
                'selling_price' => $whItem->selling_price,
                'discount' => $whItem->discount,
                'stock' => $whItem->stock,
            ]);
        }

        // 6. Stock Entries
        for ($i = 0; $i < 20; $i++) {
            $whItem = collect($warehouseItems)->random();
            \App\Models\StockEntry::factory()->create([
                'warehouse_item_id' => $whItem->id,
                'supplier_id' => $whItem->supplier_id,
            ]);
        }

        // 7. Transactions & Details
        $cashierItems = \App\Models\CashierItem::all();
        $members = \App\Models\Member::all();
        $user = \App\Models\User::first() ?: \App\Models\User::factory()->create();

        for ($i = 0; $i < 30; $i++) {
            $transaction = \App\Models\Transaction::factory()->create([
                'user_id' => $user->id,
                'member_id' => fake()->boolean(40) ? $members->random()->id : null,
                'created_at' => $i < 15 ? now() : now()->subDays(fake()->numberBetween(1, 30)),
            ]);

            // Add 1-3 details per transaction
            $detailsCount = fake()->numberBetween(1, 4);
            $total = 0;
            for ($j = 0; $j < $detailsCount; $j++) {
                $item = $cashierItems->random();
                $qty = fake()->numberBetween(1, 3);
                $subtotal = $item->selling_price * $qty;

                \App\Models\TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $item->id,
                    'price' => $item->selling_price,
                    'original_price' => $item->selling_price + 2000,
                    'discount' => $item->discount,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
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
