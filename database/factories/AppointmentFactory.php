<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $appointmentDate = $this->faker->dateTimeBetween('now', '+30 days');
        
        return [
            'client_id' => Client::factory(),
            'employee_id' => Employee::factory(),
            'service_id' => Service::factory(),
            'appointment_date' => $appointmentDate->format('Y-m-d'),
            'appointment_time' => $this->faker->time('H:i'),
            'duration' => $this->faker->numberBetween(30, 180),
            'price' => $this->faker->randomFloat(2, 200, 2000),
            'status' => $this->faker->randomElement(['scheduled', 'confirmed', 'completed', 'cancelled']),
            'notes' => $this->faker->optional()->sentence(),
            'cancellation_reason' => null,
        ];
    }
}

