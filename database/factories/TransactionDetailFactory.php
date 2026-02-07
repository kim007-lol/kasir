<?php

namespace Database\Factories;

use App\Models\TransactionDetail;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionDetailFactory extends Factory
{
    protected $model = TransactionDetail::class;

    public function definition(): array
    {
        return [
            'item_id' => Item::inRandomOrder()->first()->id ?? Item::factory(),
            'price' => 0, // Should be set from item
            'qty' => $this->faker->numberBetween(1, 5),
            'subtotal' => 0, // Should be calculated
        ];
    }
}
