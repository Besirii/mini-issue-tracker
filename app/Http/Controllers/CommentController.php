<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CommentController extends Controller
{
    /**
     * Return a paginated page of comments for an issue as rendered HTML.
     */
    public function index(Issue $issue): JsonResponse
    {
        $comments = $issue->comments()
            ->latest()
            ->paginate(5);

        $html = $comments
            ->map(fn ($comment) => view('comments.partials.comment', compact('comment'))->render())
            ->implode('');

        return response()->json([
            'html' => $html,
            'current_page' => $comments->currentPage(),
            'last_page' => $comments->lastPage(),
            'has_more' => $comments->hasMorePages(),
            'total' => $comments->total(),
        ]);
    }

    /**
     * Store a new comment and return its rendered markup so the client can
     * prepend it to the list without a page reload.
     */
    public function store(StoreCommentRequest $request, Issue $issue): JsonResponse
    {
        $comment = $issue->comments()->create($request->validated());

        return response()->json([
            'html' => view('comments.partials.comment', compact('comment'))->render(),
            'total' => $issue->comments()->count(),
        ], 201);
    }
}
