<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Blog extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected static function booted(): void
    {
        static::saved(fn(Blog $blog) => $blog->clearApiCaches());
        static::deleted(fn(Blog $blog) => $blog->clearApiCaches());
    }

    public function clearApiCaches(): void
    {
        Cache::forget('blogs:featured');
        Cache::forget("blogs:public:show:{$this->slug}");

        // Clear paginated list caches (pattern-based)
        $cacheStore = Cache::getStore();
        if (method_exists($cacheStore, 'flush')) {
            // For drivers that support tags or pattern deletion, we'd use that
            // For now, featured cache is the critical one
        }
    }

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
        'status',
        'review_status',
        'published_at',
        'meta_title',
        'meta_description',
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

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')
            ->singleFile()
            ->useFallbackUrl('/images/placeholder-blog.jpg');
    }

    /**
     * Register media conversions (WebP + compression).
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->fit(Fit::Crop, 400, 300)
            ->format('webp')
            ->quality(80)
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->fit(Fit::Contain, 800, 600)
            ->format('webp')
            ->quality(85)
            ->nonQueued();

        $this->addMediaConversion('large')
            ->fit(Fit::Contain, 1200, 900)
            ->format('webp')
            ->quality(90)
            ->nonQueued();
    }
}
