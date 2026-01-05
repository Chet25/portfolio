<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Project extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'url_live',
        'url_repo',
        'started_at',
        'completed_at',
        'status',
        'is_featured',
        'sort_order',
        'user_id',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'started_at' => 'date',
        'completed_at' => 'date',
        'sort_order' => 'integer',
    ];

    /**
     * The owner/author of the project.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tech Stack (Tags) assigned to this project.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'project_tags')
            ->withTimestamps();
    }

    /**
     * Scope: only featured projects.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
            ->orderBy('sort_order', 'asc');
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')
            ->singleFile()
            ->useFallbackUrl('/images/placeholder-project.jpg');

        $this->addMediaCollection('gallery');
    }

    /**
     * Register media conversions for thumbnails and galleries.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('card')
            ->fit(Fit::Crop, 600, 400)
            ->format('webp')
            ->nonQueued();

        $this->addMediaConversion('large')
            ->fit(Fit::Contain, 1200, 800)
            ->format('webp')
            ->nonQueued();
    }
}