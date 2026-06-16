@php
    $priorityMap = [
        'low' => 'text-bg-light border',
        'medium' => 'text-bg-warning',
        'high' => 'text-bg-danger',
    ];
@endphp
<span class="badge {{ $priorityMap[$priority] ?? 'text-bg-light' }}">
    {{ ucfirst($priority) }}
</span>
