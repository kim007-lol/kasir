<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
            'code' => strtoupper($this->faker->unique()->bothify('ITEM-####')),
            'name' => ucfirst($this->faker->words(2, true)),
            'price' => $this->faker->numberBetween(10, 500) * 1000,
            'stock' => $this->faker->numberBetween(10, 100),
        ];
    }
}
