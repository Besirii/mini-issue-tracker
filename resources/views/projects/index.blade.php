@extends('layouts.app')

@section('title', 'Projects')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Projects</h1>
        @auth
            <a class="btn btn-primary" href="{{ route('projects.create') }}">New project</a>
        @endauth
    </div>

    @if ($projects->isEmpty())
        <div class="empty-state">
            <p class="mb-2">No projects yet.</p>
            @auth
                <a class="btn btn-sm btn-primary" href="{{ route('projects.create') }}">Create the first one</a>
            @else
                <a class="btn btn-sm btn-primary" href="{{ route('login') }}">Log in to create one</a>
            @endauth
        </div>
    @else
        <div class="row g-3">
            @foreach ($projects as $project)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h2 class="h5 mb-1">
                                    <a class="text-decoration-none" href="{{ route('projects.show', $project) }}">
                                        {{ $project->name }}
                                    </a>
                                </h2>
                                <span class="badge text-bg-light">{{ $project->issues_count }} issues</span>
                            </div>
                            <p class="text-muted small mb-2">
                                {{ $project->description ? \Illuminate\Support\Str::limit($project->description, 90) : 'No description.' }}
                            </p>
                            <div class="small text-muted">
                                @if ($project->deadline)
                                    Deadline: {{ $project->deadline->format('M j, Y') }}
                                @endif
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 text-muted small">
                            Owner: {{ $project->owner?->name ?? '—' }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $projects->links() }}
        </div>
    @endif
@endsection
