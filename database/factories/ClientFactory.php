<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'phone' => '+380' . $this->faker->numerify('#########'),
            'email' => $this->faker->unique()->safeEmail(),
            'date_of_birth' => $this->faker->dateTimeBetween('-80 years', '-18 years'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'address' => $this->faker->address(),
            'notes' => $this->faker->optional()->sentence(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'loyalty_points' => $this->faker->numberBetween(0, 1000),
            'loyalty_level' => $this->faker->randomElement(['bronze', 'silver', 'gold', 'platinum']),
            'total_loyalty_points_earned' => $this->faker->numberBetween(0, 5000),
        ];
    }
}

