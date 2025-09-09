<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Blog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'thumbnail',
        'status',
        'review_status',
        'published_at',
        'meta_title',
        'meta_description',
        'canonical_url',
        'views',
        'likes',
        'reading_time',
        'is_featured',
        'user_id',
        'editor_id',
    ];

    protected $casts = [
        'is_featured'  => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Author of the blog (creator).
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Editor/reviewer of the blog.
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    /**
     * Categories assigned to this blog.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'blog_categories')
            ->withTimestamps();
    }

    /**
     * Tags assigned to this blog.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'blog_tags')
            ->withTimestamps();
    }

    /**
     * Comments on the blog.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Scope: only published blogs.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope: featured blogs.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
            ->where('status', 'published');
    }

    /**
     * Scope: only draft blogs.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Route model binding will use slug.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
