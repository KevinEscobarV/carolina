<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'agreement_date' => $this->faker->date(),
            'amount' => $this->faker->randomFloat(2, 0, 1000),
            'payment_date' => $this->faker->date(),
            'paid_amount' => $this->faker->randomFloat(2, 0, 1000),
            'payment_method' => $this->faker->randomElement(\App\Enums\PaymentMethod::cases()),
            'observations' => $this->faker->text(),
            'bill_path' => $this->faker->word(),
        ];
    }
}
