@extends('layouts.app')

@section('title', $project->name)

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
            <li class="breadcrumb-item active">{{ $project->name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h1 class="h3 mb-1">{{ $project->name }}</h1>
            <p class="text-muted mb-2">{{ $project->description ?: 'No description.' }}</p>
            <div class="small text-muted">
                Owner: {{ $project->owner?->name ?? '—' }}
                @if ($project->start_date) &middot; Start: {{ $project->start_date->format('M j, Y') }} @endif
                @if ($project->deadline) &middot; Deadline: {{ $project->deadline->format('M j, Y') }} @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            @auth
                <a class="btn btn-sm btn-primary" href="{{ route('issues.create', ['project_id' => $project->id]) }}">
                    Add issue
                </a>
            @endauth
            @can('update', $project)
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('projects.edit', $project) }}">Edit</a>
            @endcan
            @can('delete', $project)
                <form method="POST" action="{{ route('projects.destroy', $project) }}"
                      onsubmit="return confirm('Delete this project and all its issues?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
            @endcan
        </div>
    </div>

    <h2 class="h5 mb-3">Issues ({{ $project->issues->count() }})</h2>

    @if ($project->issues->isEmpty())
        <div class="empty-state">No issues in this project yet.</div>
    @else
        <div class="list-group">
            @foreach ($project->issues as $issue)
                <a href="{{ route('issues.show', $issue) }}"
                   class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-medium">{{ $issue->title }}</span>
                        <span class="d-flex gap-1 align-items-center">
                            @include('issues.partials.priority-badge', ['priority' => $issue->priority])
                            @include('issues.partials.status-badge', ['status' => $issue->status])
                        </span>
                    </div>
                    <div class="mt-1 d-flex flex-wrap gap-1 align-items-center">
                        @foreach ($issue->tags as $tag)
                            @include('issues.partials.tag-chip', ['tag' => $tag])
                        @endforeach
                        <span class="text-muted small ms-auto">{{ $issue->comments_count }} comments</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection
