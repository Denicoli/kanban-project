<?php

namespace Database\Factories;

use App\Models\Column;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'column_id' => Column::factory(),
            'title' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
