<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(3),
            'date' => fake()->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
            'created_by' => User::factory(),
            'location' => fake()->city() . ', ' . fake()->state(),
        ];
    }
}
