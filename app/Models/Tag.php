<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
    ];

    /**
     * Blogs that belong to this tag.
     */
    public function blogs(): BelongsToMany
    {
        return $this->belongsToMany(Blog::class, 'blog_tags')
            ->withTimestamps();
    }

    /**
     * Only published blogs for this tag.
     */
    public function publishedBlogs(): BelongsToMany
    {
        return $this->belongsToMany(Blog::class, 'blog_tags')
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->withTimestamps();
    }

    /**
     * Route model binding will use slug instead of id.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
