<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'author_name',
        'author_email',
        'status',
        'blog_id',
        'user_id',
        'parent_id', // Added for nested comments
    ];

    protected $casts = [
        'status' => 'string',
        'author_email' => 'string', // Cast as string (nullable)
    ];

    /**
     * Blog that this comment belongs to.
     */
    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    /**
     * User who wrote the comment (nullable for guest).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Parent comment (for nested/threaded comments).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Replies to this comment (for nested/threaded comments).
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Scope: only approved comments.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: only pending comments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: only rejected comments.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Computed author name (user or guest).
     */
    public function getAuthorNameAttribute($value): string
    {
        return $this->user?->name ?? $value ?? 'Guest';
    }

    /**
     * Computed author email (user or guest).
     */
    public function getAuthorEmailAttribute($value): ?string
    {
        return $this->user?->email ?? $value;
    }
}
