{{-- Expects: $action, $method, $issue (Issue|null), $projects, $tags,
     $statuses, $priorities, $selectedTagIds (array), $selectedProjectId (int|null), $submitLabel --}}
@php
    $selectedTagIds = $selectedTagIds ?? [];
    $currentProject = old('project_id', $issue?->project_id ?? ($selectedProjectId ?? ''));
@endphp
<form method="POST" action="{{ $action }}">
    @csrf
    @if (($method ?? 'POST') === 'PUT')
        @method('PUT')
    @endif

    <div class="mb-3">
        <label class="form-label" for="project_id">Project <span class="text-danger">*</span></label>
        <select id="project_id" name="project_id"
                class="form-select @error('project_id') is-invalid @enderror" required>
            <option value="">— Select a project —</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}" @selected((string)$currentProject === (string)$project->id)>
                    {{ $project->name }}
                </option>
            @endforeach
        </select>
        @error('project_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
        <input id="title" name="title" type="text"
               class="form-control @error('title') is-invalid @enderror"
               value="{{ old('title', $issue?->title) }}" required>
        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label" for="description">Description</label>
        <textarea id="description" name="description" rows="4"
                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $issue?->description) }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                @foreach ($statuses as $status)
                    <option value="{{ $status }}"
                        @selected(old('status', $issue?->status ?? 'open') === $status)>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                @endforeach
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label" for="priority">Priority <span class="text-danger">*</span></label>
            <select id="priority" name="priority" class="form-select @error('priority') is-invalid @enderror">
                @foreach ($priorities as $priority)
                    <option value="{{ $priority }}"
                        @selected(old('priority', $issue?->priority ?? 'medium') === $priority)>
                        {{ ucfirst($priority) }}
                    </option>
                @endforeach
            </select>
            @error('priority') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label" for="due_date">Due date</label>
            <input id="due_date" name="due_date" type="date"
                   class="form-control @error('due_date') is-invalid @enderror"
                   value="{{ old('due_date', $issue?->due_date?->format('Y-m-d')) }}">
            @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label" for="tags">Tags</label>
        <select id="tags" name="tags[]" class="form-select" multiple size="5">
            @foreach ($tags as $tag)
                <option value="{{ $tag->id }}"
                    @selected(collect(old('tags', $selectedTagIds))->map(fn($v) => (string)$v)->contains((string)$tag->id))>
                    {{ $tag->name }}
                </option>
            @endforeach
        </select>
        <div class="form-text">Hold Ctrl / Cmd to select multiple. You can also manage tags from the issue page.</div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary">{{ $submitLabel ?? 'Save' }}</button>
        <a class="btn btn-outline-secondary" href="{{ url()->previous() }}">Cancel</a>
    </div>
</form>
