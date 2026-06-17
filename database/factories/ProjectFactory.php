<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = Carbon::instance(fake()->dateTimeBetween('-1 month', '+1 week'));

        return [
            'name' => ucfirst(fake()->unique()->words(2, true)),
            'description' => fake()->paragraph(),
            'start_date' => $start,
            'deadline' => (clone $start)->addDays(fake()->numberBetween(14, 90)),
        ];
    }
}
