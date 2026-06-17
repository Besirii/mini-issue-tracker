<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with demo data.
     */
    public function run(): void
    {
        // Demo users with known passwords ("password") for the reviewer.
        $alice = User::factory()->create([
            'name' => 'Alice Owner',
            'email' => 'alice@example.com',
        ]);

        $bob = User::factory()->create([
            'name' => 'Bob Owner',
            'email' => 'bob@example.com',
        ]);

        $extraMembers = User::factory(4)->create();
        $allUsers = collect([$alice, $bob])->merge($extraMembers);

        // A curated set of tags.
        $tags = collect([
            ['name' => 'bug', 'color' => '#dc2626'],
            ['name' => 'feature', 'color' => '#2563eb'],
            ['name' => 'enhancement', 'color' => '#16a34a'],
            ['name' => 'documentation', 'color' => '#0891b2'],
            ['name' => 'urgent', 'color' => '#d97706'],
            ['name' => 'backend', 'color' => '#7c3aed'],
            ['name' => 'frontend', 'color' => '#db2777'],
            ['name' => 'wontfix', 'color' => '#475569'],
        ])->map(fn (array $tag) => Tag::firstOrCreate(['name' => $tag['name']], ['color' => $tag['color']]));

        // Each owner gets a couple of projects, each with issues, tags,
        // members and comments.
        collect([$alice, $bob])->each(function (User $owner) use ($tags, $allUsers): void {
            Project::factory(2)
                ->for($owner, 'owner')
                ->create()
                ->each(function (Project $project) use ($tags, $allUsers): void {
                    Issue::factory(fake()->numberBetween(3, 6))
                        ->for($project)
                        ->create()
                        ->each(function (Issue $issue) use ($tags, $allUsers): void {
                            $issue->tags()->sync(
                                $tags->random(fake()->numberBetween(1, 3))->pluck('id')
                            );

                            $memberCount = fake()->numberBetween(0, 2);
                            if ($memberCount > 0) {
                                $issue->members()->sync(
                                    $allUsers->random($memberCount)->pluck('id')
                                );
                            }

                            Comment::factory(fake()->numberBetween(0, 5))
                                ->for($issue)
                                ->create();
                        });
                });
        });
    }
}
