{{-- Expects: $comment --}}
<div class="comment" data-comment-id="{{ $comment->id }}">
    <div class="comment__header">
        <span class="comment__author">{{ $comment->author_name }}</span>
        <span class="comment__time">{{ $comment->created_at->diffForHumans() }}</span>
    </div>
    <div class="comment__body">{{ $comment->body }}</div>
</div>
