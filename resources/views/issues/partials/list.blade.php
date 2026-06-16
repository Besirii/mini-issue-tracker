{{-- Expects: $issues (paginator) --}}
@if ($issues->isEmpty())
    <div class="empty-state">No issues match these filters.</div>
@else
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Project</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Tags</th>
                        <th class="text-end">Comments</th>
                        <th>Due</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($issues as $issue)
                        <tr>
                            <td>
                                <a class="text-decoration-none fw-medium" href="{{ route('issues.show', $issue) }}">
                                    {{ $issue->title }}
                                </a>
                            </td>
                            <td class="text-muted small">{{ $issue->project->name }}</td>
                            <td>@include('issues.partials.priority-badge', ['priority' => $issue->priority])</td>
                            <td>@include('issues.partials.status-badge', ['status' => $issue->status])</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach ($issue->tags as $tag)
                                        @include('issues.partials.tag-chip', ['tag' => $tag])
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-end">{{ $issue->comments_count }}</td>
                            <td class="text-muted small">
                                {{ $issue->due_date ? $issue->due_date->format('M j, Y') : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $issues->links() }}
    </div>
@endif
