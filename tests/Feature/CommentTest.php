<?php

namespace Tests\Feature;

use App\Models\Issue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_comment_can_be_added_via_ajax(): void
    {
        $issue = Issue::factory()->create();

        $this->postJson(route('issues.comments.store', $issue), [
            'author_name' => 'Jane Doe',
            'body' => 'Looks good to me.',
        ])
            ->assertCreated()
            ->assertJsonStructure(['html', 'total']);

        $this->assertDatabaseHas('comments', [
            'issue_id' => $issue->id,
            'author_name' => 'Jane Doe',
        ]);
    }

    public function test_a_comment_requires_author_name_and_body(): void
    {
        $issue = Issue::factory()->create();

        $this->postJson(route('issues.comments.store', $issue), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['author_name', 'body']);
    }

    public function test_comments_are_paginated(): void
    {
        $issue = Issue::factory()->create();
        $issue->comments()->createMany(
            collect(range(1, 7))->map(fn ($i) => [
                'author_name' => "User {$i}",
                'body' => "Comment {$i}",
            ])->all()
        );

        $this->getJson(route('issues.comments.index', $issue))
            ->assertOk()
            ->assertJson([
                'current_page' => 1,
                'last_page' => 2,
                'has_more' => true,
                'total' => 7,
            ]);
    }
}
