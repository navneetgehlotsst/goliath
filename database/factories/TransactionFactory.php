<?php

namespace Database\Factories;

use App\Models\Transaction; // Import the Transaction model
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::all()->random()->id,
            'amount' => $this->faker->randomFloat(2, 1, 1000), // Adjust according to your needs
            'transaction_id' => Str::random(10),
        ];
    }
}
