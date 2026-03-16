<?php

namespace Database\Factories;

use App\Models\ExpenseSplit;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExpenseSplit>
 */
class ExpenseSplitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'expense_id' => Expense::factory(),
            'user_id' => User::factory(),
            'member_email' => null,
            'amount' => fake()->randomFloat(2, 1, 1000),
            'settled' => false,
        ];
    }
}
