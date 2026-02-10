<?php

namespace Database\Factories;

use App\Models\TransactionDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionDetailFactory extends Factory
{
    protected $model = TransactionDetail::class;

    public function definition(): array
    {
        return [
            'transaction_id' => \App\Models\Transaction::factory(),
            'item_id' => \App\Models\CashierItem::factory(),
            'price' => fake()->numberBetween(5000, 100000),
            'original_price' => fake()->numberBetween(5000, 100000),
            'discount' => fake()->randomElement([0, 5, 10]),
            'qty' => fake()->numberBetween(1, 5),
            'subtotal' => 0, // Should be calculated
        ];
    }
}
