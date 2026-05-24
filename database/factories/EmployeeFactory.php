<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'specialization' => $this->faker->randomElement(['Стрижка', 'Фарбування', 'Манікюр', 'Педикюр', 'Макіяж']),
            'bio' => $this->faker->optional()->paragraph(),
            'rating' => $this->faker->randomFloat(2, 3.0, 5.0),
            'hire_date' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'work_start_time' => '09:00',
            'work_end_time' => '18:00',
            'min_break_between_appointments' => 15,
        ];
    }
}

