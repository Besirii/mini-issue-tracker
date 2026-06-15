<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    /**
     * List issues with filters (status, priority, tag) and text search.
     * Returns only the list partial for AJAX requests so the page never reloads.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'priority', 'tag', 'search']);

        $issues = Issue::query()
            ->with(['project', 'tags'])
            ->withCount('comments')
            ->filter($filters)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('issues.partials.list', compact('issues'));
        }

        return view('issues.index', [
            'issues' => $issues,
            'projects' => Project::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
            'filters' => $filters,
            'statuses' => Issue::STATUSES,
            'priorities' => Issue::PRIORITIES,
        ]);
    }

    public function create(Request $request): View
    {
        return view('issues.create', [
            'projects' => Project::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
            'statuses' => Issue::STATUSES,
            'priorities' => Issue::PRIORITIES,
            'selectedProjectId' => $request->integer('project_id'),
        ]);
    }

    public function store(StoreIssueRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $tagIds = $data['tags'] ?? [];
        unset($data['tags']);

        $issue = Issue::create($data);
        $issue->tags()->sync($tagIds);

        return redirect()
            ->route('issues.show', $issue)
            ->with('status', 'Issue created.');
    }

    public function show(Issue $issue): View
    {
        $issue->load(['project', 'tags', 'members']);

        return view('issues.show', [
            'issue' => $issue,
            'allTags' => Tag::orderBy('name')->get(),
            'allUsers' => \App\Models\User::orderBy('name')->get(),
        ]);
    }

    public function edit(Issue $issue): View
    {
        $issue->load('tags');

        return view('issues.edit', [
            'issue' => $issue,
            'projects' => Project::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
            'statuses' => Issue::STATUSES,
            'priorities' => Issue::PRIORITIES,
            'selectedTagIds' => $issue->tags->pluck('id')->all(),
        ]);
    }

    public function update(UpdateIssueRequest $request, Issue $issue): RedirectResponse
    {
        $data = $request->validated();
        $tagIds = $data['tags'] ?? [];
        unset($data['tags']);

        $issue->update($data);
        $issue->tags()->sync($tagIds);

        return redirect()
            ->route('issues.show', $issue)
            ->with('status', 'Issue updated.');
    }

    public function destroy(Issue $issue): RedirectResponse
    {
        $projectId = $issue->project_id;
        $issue->delete();

        return redirect()
            ->route('projects.show', $projectId)
            ->with('status', 'Issue deleted.');
    }
}
