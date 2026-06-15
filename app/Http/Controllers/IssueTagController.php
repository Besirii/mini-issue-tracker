<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttachTagRequest;
use App\Models\Issue;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

class IssueTagController extends Controller
{
    /**
     * Attach a tag to the issue (AJAX). Idempotent thanks to syncWithoutDetaching.
     */
    public function store(AttachTagRequest $request, Issue $issue): JsonResponse
    {
        $tag = Tag::findOrFail($request->integer('tag_id'));

        $issue->tags()->syncWithoutDetaching([$tag->id]);

        return response()->json([
            'html' => view('issues.partials.tag-pill', compact('issue', 'tag'))->render(),
            'tag' => $tag->only(['id', 'name', 'color']),
        ], 201);
    }

    /**
     * Detach a tag from the issue (AJAX).
     */
    public function destroy(Issue $issue, Tag $tag): JsonResponse
    {
        $issue->tags()->detach($tag->id);

        return response()->json([
            'detached' => true,
            'tag' => $tag->only(['id', 'name', 'color']),
        ]);
    }
}
