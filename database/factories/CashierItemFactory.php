<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CashierItem>
 */
class CashierItemFactory extends Factory
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
            'category_id' => \App\Models\Category::factory(),
            'supplier_id' => \App\Models\Supplier::factory(),
            'code' => strtoupper(fake()->unique()->bothify('CS-####??')),
            'name' => fake()->words(3, true),
            'selling_price' => fake()->numberBetween(5000, 100000),
            'discount' => fake()->randomElement([0, 5, 10]),
            'stock' => fake()->numberBetween(0, 50),
        ];
    }
}
