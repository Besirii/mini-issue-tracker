{{-- Expects: $tag --}}
<span class="tag-chip" @if($tag->color) style="--tag-color: {{ $tag->color }}" @endif>
    {{ $tag->name }}
</span>
