{{-- Expects: $issue, $tag --}}
<span class="tag-pill" data-tag-id="{{ $tag->id }}"
      @if($tag->color) style="--tag-color: {{ $tag->color }}" @endif>
    <span class="tag-pill__label">{{ $tag->name }}</span>
    @auth
        <button type="button" class="tag-pill__remove" aria-label="Detach tag"
                data-url="{{ route('issues.tags.destroy', [$issue, $tag]) }}">&times;</button>
    @endauth
</span>
