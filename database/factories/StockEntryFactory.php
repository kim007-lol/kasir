<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockEntry>
 */
class StockEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'warehouse_item_id' => \App\Models\WarehouseItem::factory(),
            'supplier_id' => \App\Models\Supplier::factory(),
            'quantity' => fake()->numberBetween(10, 50),
            'entry_date' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
