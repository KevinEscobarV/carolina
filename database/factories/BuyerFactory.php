<?php

namespace Database\Factories;

use App\Enums\CivilStatus;
use App\Enums\DocumentType;
use App\Models\Buyer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Buyer>
 */
class BuyerFactory extends Factory
{
    protected $model = Buyer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'names' => $this->faker->name,
            'surnames' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'document_type' => $this->faker->randomElement(DocumentType::cases()),
            'document_number' => $this->faker->unique()->randomNumber(8),
            'civil_status' => $this->faker->randomElement(CivilStatus::cases()),
            'phone_one' => $this->faker->phoneNumber,
            'phone_two' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
        ];
    }
}
