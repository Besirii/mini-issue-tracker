@extends('layouts.app')

@section('title', 'Issues')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Issues</h1>
        @auth
            <a class="btn btn-primary" href="{{ route('issues.create') }}">New issue</a>
        @endauth
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form id="issue-filters" class="row g-2 align-items-end" data-url="{{ route('issues.index') }}">
                <div class="col-sm-6 col-md-3">
                    <label class="form-label small mb-1" for="f-status">Status</label>
                    <select id="f-status" name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-md-3">
                    <label class="form-label small mb-1" for="f-priority">Priority</label>
                    <select id="f-priority" name="priority" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach ($priorities as $priority)
                            <option value="{{ $priority }}" @selected(($filters['priority'] ?? '') === $priority)>
                                {{ ucfirst($priority) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-md-3">
                    <label class="form-label small mb-1" for="f-tag">Tag</label>
                    <select id="f-tag" name="tag" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach ($tags as $tag)
                            <option value="{{ $tag->id }}" @selected((string)($filters['tag'] ?? '') === (string)$tag->id)>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-md-3">
                    <label class="form-label small mb-1" for="f-search">Search</label>
                    <input id="f-search" name="search" type="search" value="{{ $filters['search'] ?? '' }}"
                           class="form-control form-control-sm" placeholder="Title or description…" autocomplete="off">
                </div>
            </form>
        </div>
    </div>

    <div id="issues-list" data-url="{{ route('issues.index') }}">
        @include('issues.partials.list')
    </div>

    @push('scripts')
        <script src="{{ asset('js/issues-index.js') }}"></script>
    @endpush
@endsection
