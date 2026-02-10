<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WarehouseItem>
 */
class WarehouseItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $purchasePrice = fake()->numberBetween(1000, 50000);
        return [
            'category_id' => \App\Models\Category::factory(),
            'supplier_id' => \App\Models\Supplier::factory(),
            'code' => strtoupper(fake()->unique()->bothify('WH-####??')),
            'name' => fake()->words(3, true),
            'purchase_price' => $purchasePrice,
            'selling_price' => $purchasePrice * 1.5,
            'discount' => fake()->randomElement([0, 0, 5, 10]),
            'stock' => fake()->numberBetween(0, 100),
            'exp_date' => fake()->dateTimeBetween('now', '+2 years'),
        ];
    }
}
