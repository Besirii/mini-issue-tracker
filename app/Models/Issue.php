<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Issue extends Model
{
    /** @use HasFactory<\Database\Factories\IssueFactory> */
    use HasFactory;

    public const STATUSES = ['open', 'in_progress', 'closed'];

    public const PRIORITIES = ['low', 'medium', 'high'];

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    /**
     * Assigned members (bonus). Pivot table: issue_user.
     *
     * @return BelongsToMany<User, $this>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Apply the list filters (status, priority, tag) and the text search.
     *
     * @param  Builder<Issue>  $query
     * @param  array<string, mixed>  $filters
     * @return Builder<Issue>
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(
                ! empty($filters['status']),
                fn (Builder $q) => $q->where('status', $filters['status'])
            )
            ->when(
                ! empty($filters['priority']),
                fn (Builder $q) => $q->where('priority', $filters['priority'])
            )
            ->when(
                ! empty($filters['tag']),
                fn (Builder $q) => $q->whereHas(
                    'tags',
                    fn (Builder $tagQuery) => $tagQuery->whereKey($filters['tag'])
                )
            )
            ->when(
                ! empty($filters['search']),
                function (Builder $q) use ($filters): void {
                    $term = '%'.$filters['search'].'%';
                    $q->where(function (Builder $inner) use ($term): void {
                        $inner->where('title', 'like', $term)
                            ->orWhere('description', 'like', $term);
                    });
                }
            );
    }
}
