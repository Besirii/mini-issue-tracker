<?php

namespace Database\Factories;

use App\Models\Issue;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Issue>
 */
class IssueFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'title' => rtrim(fake()->sentence(4), '.'),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(Issue::STATUSES),
            'priority' => fake()->randomElement(Issue::PRIORITIES),
            'due_date' => fake()->boolean(70)
                ? fake()->dateTimeBetween('-5 days', '+30 days')
                : null,
        ];
    }
}
