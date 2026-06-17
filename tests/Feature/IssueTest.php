<?php

namespace Tests\Feature;

use App\Models\Issue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssueTest extends TestCase
{
    use RefreshDatabase;

    public function test_issues_can_be_filtered_by_status(): void
    {
        Issue::factory()->create(['title' => 'Open issue alpha', 'status' => 'open']);
        Issue::factory()->create(['title' => 'Closed issue beta', 'status' => 'closed']);

        $this->get(route('issues.index', ['status' => 'open']))
            ->assertOk()
            ->assertSee('Open issue alpha')
            ->assertDontSee('Closed issue beta');
    }

    public function test_ajax_index_returns_only_the_list_partial(): void
    {
        Issue::factory()->create(['title' => 'Searchable issue title']);

        $response = $this->get(
            route('issues.index', ['search' => 'Searchable']),
            ['X-Requested-With' => 'XMLHttpRequest']
        );

        $response->assertOk()
            ->assertSee('Searchable issue title')
            ->assertDontSee('<nav', false); // navbar from the full layout is absent in the partial
    }

    public function test_creating_an_issue_requires_authentication(): void
    {
        $this->post(route('issues.store'), [])->assertRedirect(route('login'));
    }
}
