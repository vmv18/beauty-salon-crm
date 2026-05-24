<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => ServiceCategory::factory(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'duration' => $this->faker->numberBetween(30, 180),
            'price' => $this->faker->randomFloat(2, 200, 2000),
            'image' => null,
            'is_active' => true,
        ];
    }
}

