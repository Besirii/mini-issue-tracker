<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_projects_index(): void
    {
        $this->get(route('projects.index'))->assertOk();
    }

    public function test_authenticated_user_can_create_a_project(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('projects.store'), ['name' => 'My Project'])
            ->assertRedirect();

        $this->assertDatabaseHas('projects', [
            'name' => 'My Project',
            'owner_id' => $user->id,
        ]);
    }

    public function test_creating_a_project_requires_a_name(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('projects.store'), ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    public function test_only_the_owner_can_update_a_project(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $project = Project::factory()->for($owner, 'owner')->create();

        $this->actingAs($other)
            ->put(route('projects.update', $project), ['name' => 'Hacked'])
            ->assertForbidden();

        $this->actingAs($owner)
            ->put(route('projects.update', $project), ['name' => 'Renamed'])
            ->assertRedirect();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Renamed',
        ]);
    }
}
