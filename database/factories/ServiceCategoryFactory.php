<?php

namespace Database\Factories;

use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceCategory>
 */
class ServiceCategoryFactory extends Factory
{
    protected $model = ServiceCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Стрижка та укладка', 'Фарбування', 'Манікюр', 'Педикюр', 'Макіяж', 'Догляд за обличчям']),
            'description' => $this->faker->optional()->sentence(),
            'image' => null,
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }
}

