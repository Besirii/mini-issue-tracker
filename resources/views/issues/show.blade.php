@extends('layouts.app')

@section('title', $issue->title)

@php
    $attachedTagIds = $issue->tags->pluck('id')->all();
    $availableTags = $allTags->whereNotIn('id', $attachedTagIds);
    $attachedMemberIds = $issue->members->pluck('id')->all();
    $availableUsers = $allUsers->whereNotIn('id', $attachedMemberIds);
@endphp

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
            <li class="breadcrumb-item"><a href="{{ route('projects.show', $issue->project) }}">{{ $issue->project->name }}</a></li>
            <li class="breadcrumb-item active">Issue #{{ $issue->id }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h1 class="h3 mb-2">{{ $issue->title }}</h1>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                @include('issues.partials.priority-badge', ['priority' => $issue->priority])
                @include('issues.partials.status-badge', ['status' => $issue->status])
                @if ($issue->due_date)
                    <span class="text-muted small">Due {{ $issue->due_date->format('M j, Y') }}</span>
                @endif
            </div>
        </div>
        @auth
            <div class="d-flex gap-2">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('issues.edit', $issue) }}">Edit</a>
                <form method="POST" action="{{ route('issues.destroy', $issue) }}"
                      onsubmit="return confirm('Delete this issue?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
            </div>
        @endauth
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted">Description</h2>
                    <p class="mb-0" style="white-space: pre-line;">{{ $issue->description ?: 'No description.' }}</p>
                </div>
            </div>

            {{-- Comments (loaded + added via AJAX) --}}
            <div class="card shadow-sm" id="comments"
                 data-index-url="{{ route('issues.comments.index', $issue) }}"
                 data-store-url="{{ route('issues.comments.store', $issue) }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 text-uppercase text-muted mb-0">Comments</h2>
                        <span class="badge text-bg-light" id="comments-count">0</span>
                    </div>

                    <form id="comment-form" class="mb-4">
                        <div class="mb-2">
                            <input name="author_name" class="form-control form-control-sm" placeholder="Your name">
                            <div class="text-danger small mt-1" data-error="author_name"></div>
                        </div>
                        <div class="mb-2">
                            <textarea name="body" rows="2" class="form-control form-control-sm"
                                      placeholder="Write a comment…"></textarea>
                            <div class="text-danger small mt-1" data-error="body"></div>
                        </div>
                        <button class="btn btn-sm btn-primary" type="submit">Add comment</button>
                    </form>

                    <div id="comments-list"></div>

                    <div class="text-center mt-3">
                        <button id="load-more-comments" class="btn btn-sm btn-outline-secondary d-none">
                            Load more
                        </button>
                        <p id="comments-empty" class="text-muted small d-none mb-0">No comments yet.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            {{-- Tags --}}
            <div class="card shadow-sm mb-4" id="tags-panel"
                 data-attach-url="{{ route('issues.tags.store', $issue) }}">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted">Tags</h2>
                    <div id="issue-tags" class="d-flex flex-wrap gap-2 mb-3">
                        @forelse ($issue->tags as $tag)
                            @include('issues.partials.tag-pill', ['issue' => $issue, 'tag' => $tag])
                        @empty
                            <span class="text-muted small" data-empty>No tags attached.</span>
                        @endforelse
                    </div>
                    @auth
                        <div class="input-group input-group-sm">
                            <select id="tag-select" class="form-select">
                                <option value="">Select a tag…</option>
                                @foreach ($availableTags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                @endforeach
                            </select>
                            <button id="attach-tag-btn" class="btn btn-outline-primary" type="button">Attach</button>
                        </div>
                    @endauth
                </div>
            </div>

            {{-- Members (bonus) --}}
            <div class="card shadow-sm" id="members-panel"
                 data-assign-url="{{ route('issues.members.store', $issue) }}">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted">Members</h2>
                    <div id="issue-members" class="d-flex flex-wrap gap-2 mb-3">
                        @forelse ($issue->members as $member)
                            @include('issues.partials.member', ['issue' => $issue, 'user' => $member])
                        @empty
                            <span class="text-muted small" data-empty>No members assigned.</span>
                        @endforelse
                    </div>
                    @auth
                        <div class="input-group input-group-sm">
                            <select id="member-select" class="form-select">
                                <option value="">Select a member…</option>
                                @foreach ($availableUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <button id="assign-member-btn" class="btn btn-outline-primary" type="button">Assign</button>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/issue-show.js') }}"></script>
    @endpush
@endsection
