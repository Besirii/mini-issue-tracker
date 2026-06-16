@php
    $statusMap = [
        'open' => 'text-bg-secondary',
        'in_progress' => 'text-bg-info',
        'closed' => 'text-bg-success',
    ];
    $statusLabels = [
        'open' => 'Open',
        'in_progress' => 'In progress',
        'closed' => 'Closed',
    ];
@endphp
<span class="badge {{ $statusMap[$status] ?? 'text-bg-secondary' }}">
    {{ $statusLabels[$status] ?? $status }}
</span>
