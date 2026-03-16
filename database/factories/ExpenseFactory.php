<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'group_id' => Group::factory(),
            'paid_by' => User::factory(),
            'description' => fake()->sentence(),
            'amount' => fake()->randomFloat(2, 1, 1000),
        ];
    }
}
