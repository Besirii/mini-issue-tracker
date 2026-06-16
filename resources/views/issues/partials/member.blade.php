{{-- Expects: $issue, $user --}}
<span class="member-chip" data-user-id="{{ $user->id }}">
    <span class="member-chip__avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
    <span class="member-chip__name">{{ $user->name }}</span>
    @auth
        <button type="button" class="member-chip__remove" aria-label="Remove member"
                data-url="{{ route('issues.members.destroy', [$issue, $user]) }}">&times;</button>
    @endauth
</span>
