<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'color' => fake()->randomElement([
                '#2563eb', '#dc2626', '#16a34a', '#d97706',
                '#7c3aed', '#0891b2', '#db2777', '#475569',
            ]),
        ];
    }
}
