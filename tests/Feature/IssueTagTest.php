<?php

namespace Tests\Feature;

use App\Models\Issue;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssueTagTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_tag_can_be_attached_and_detached_via_ajax(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->create();
        $tag = Tag::factory()->create();

        $this->actingAs($user)
            ->postJson(route('issues.tags.store', $issue), ['tag_id' => $tag->id])
            ->assertCreated()
            ->assertJsonStructure(['html', 'tag']);

        $this->assertDatabaseHas('issue_tag', [
            'issue_id' => $issue->id,
            'tag_id' => $tag->id,
        ]);

        $this->actingAs($user)
            ->deleteJson(route('issues.tags.destroy', [$issue, $tag]))
            ->assertOk();

        $this->assertDatabaseMissing('issue_tag', [
            'issue_id' => $issue->id,
            'tag_id' => $tag->id,
        ]);
    }

    public function test_attaching_a_tag_requires_authentication(): void
    {
        $issue = Issue::factory()->create();
        $tag = Tag::factory()->create();

        $this->postJson(route('issues.tags.store', $issue), ['tag_id' => $tag->id])
            ->assertUnauthorized();
    }

    public function test_attaching_the_same_tag_twice_is_idempotent(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->create();
        $tag = Tag::factory()->create();

        $this->actingAs($user)
            ->postJson(route('issues.tags.store', $issue), ['tag_id' => $tag->id])
            ->assertCreated();

        $this->actingAs($user)
            ->postJson(route('issues.tags.store', $issue), ['tag_id' => $tag->id])
            ->assertCreated();

        $this->assertEquals(1, $issue->tags()->count());
    }
}
