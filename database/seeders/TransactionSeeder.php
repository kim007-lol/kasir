<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Item;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure there are items to sell
        if (Item::count() == 0) {
            $this->command->info('No items found, skipping Transaction seeding.');
            return;
        }

        $this->command->info('Seeding Transactions for the last 7 days...');

        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::today()->subDays($i);
            $transactionCount = rand(5, 15);

            $this->command->info(" Creating $transactionCount transactions for " . $date->format('Y-m-d'));

            Transaction::factory()->count($transactionCount)->create([
                'created_at' => $date->addHours(rand(8, 20))->addMinutes(rand(0, 59)), // Business hours
                'updated_at' => $date,
            ])->each(function ($transaction) {
                // Add details
                $detailCount = rand(1, 5);
                $total = 0;

                for ($j = 0; $j < $detailCount; $j++) {
                    $item = Item::inRandomOrder()->first();
                    $qty = rand(1, 3);
                    $subtotal = $item->price * $qty;

                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'item_id' => $item->id,
                        'price' => $item->price,
                        'qty' => $qty,
                        'subtotal' => $subtotal,
                        'created_at' => $transaction->created_at,
                        'updated_at' => $transaction->created_at,
                    ]);

                    $total += $subtotal;
                }

                // Update transaction totals
                $paid = $total + rand(0, 50000); // Random paid amount
                $change = $paid - $total;

                $transaction->update([
                    'total' => $total,
                    'paid_amount' => $paid,
                    'change_amount' => $change,
                ]);
            });
        }

        $this->command->info('Transaction seeding completed.');
    }
}
