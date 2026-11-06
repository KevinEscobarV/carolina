<?php

namespace Database\Factories;

use App\Models\Quota;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Quotas belong to a promise: create them with ->for($promise).
 *
 * @extends Factory<Quota>
 */
class QuotaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = fake()->numberBetween(1, 20) * 100_000;

        return [
            'number' => 1,
            'due_date' => fake()->date(),
            'amount' => $amount,
            'interest' => 0,
            'principal' => $amount,
            'balance' => 0,
            'is_extraordinary' => false,
        ];
    }
}
