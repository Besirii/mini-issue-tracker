<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignMemberRequest;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class IssueMemberController extends Controller
{
    /**
     * Assign a member to the issue (AJAX).
     */
    public function store(AssignMemberRequest $request, Issue $issue): JsonResponse
    {
        $user = User::findOrFail($request->integer('user_id'));

        $issue->members()->syncWithoutDetaching([$user->id]);

        return response()->json([
            'html' => view('issues.partials.member', compact('issue', 'user'))->render(),
            'user' => $user->only(['id', 'name']),
        ], 201);
    }

    /**
     * Remove a member from the issue (AJAX).
     */
    public function destroy(Issue $issue, User $user): JsonResponse
    {
        $issue->members()->detach($user->id);

        return response()->json([
            'detached' => true,
            'user' => $user->only(['id', 'name']),
        ]);
    }
}
