<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'invoice' => 'INV-' . strtoupper(fake()->unique()->bothify('####??')),
            'customer_name' => fake()->name(),
            'total' => fake()->numberBetween(10000, 500000),
            'user_id' => \App\Models\User::first()->id ?? \App\Models\User::factory(),
            'paid_amount' => 500000,
            'change_amount' => 0,
            'payment_method' => fake()->randomElement(['cash', 'qris']),
            'member_id' => fake()->boolean(30) ? \App\Models\Member::factory() : null,
            'discount_percent' => fake()->randomElement([0, 0, 5, 10]),
            'discount_amount' => 0,
            'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
