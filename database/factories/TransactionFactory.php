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
            'invoice' => 'INV-' . strtoupper($this->faker->unique()->bothify('####??')),
            'customer_name' => $this->faker->name(),
            'total' => 0, // Will be calculated after adding details
            'user_id' => User::first()->id ?? User::factory(),
            'paid_amount' => 0,
            'change_amount' => 0,
            'payment_method' => $this->faker->randomElement(['cash', 'qris']),
            'created_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ];
    }
}
