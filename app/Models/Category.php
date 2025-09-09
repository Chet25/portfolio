<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
    ];

    protected $casts = [
        'color' => 'string', // e.g., store hex codes like #ff5733
    ];

    /**
     * Blogs that belong to this category.
     * Includes timestamps on the pivot table.
     */
    public function blogs(): BelongsToMany
    {
        return $this->belongsToMany(Blog::class, 'blog_categories')->withTimestamps();
    }

    /**
     * Only published blogs in this category.
     */
    public function publishedBlogs(): BelongsToMany
    {
        return $this->belongsToMany(Blog::class, 'blog_categories')
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->withTimestamps();
    }

    /**
     * Use slug for route model binding.
     * Ensure slug is unique in the migration.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Scope a query to only include featured categories.
     *
     * Usage: Category::featured()->get();
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
}
