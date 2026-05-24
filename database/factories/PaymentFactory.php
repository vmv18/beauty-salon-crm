<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Client;
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
            'appointment_id' => Appointment::factory(),
            'client_id' => Client::factory(),
            'amount' => $this->faker->randomFloat(2, 100, 2000),
            'payment_method' => $this->faker->randomElement(['cash', 'card', 'online', 'bank_transfer']),
            'payment_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed', 'refunded']),
            'notes' => $this->faker->optional()->sentence(),
            'document' => null,
        ];
    }
}

