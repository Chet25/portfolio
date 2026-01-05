<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->when(
                $request->routeIs('blogs.show'),
                $this->content
            ),
            'featured_image' => $this->getFirstMediaUrl('featured_image', 'large') ?: null,
            'featured_image_medium' => $this->getFirstMediaUrl('featured_image', 'medium') ?: null,
            'thumbnail' => $this->getFirstMediaUrl('featured_image', 'thumbnail') ?: null,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'reading_time' => $this->reading_time,
            'views' => $this->views,
            'likes' => $this->likes,
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),

            // SEO fields (only on detail view)
            'meta_title' => $this->when(
                $request->routeIs('blogs.show'),
                $this->meta_title
            ),
            'meta_description' => $this->when(
                $request->routeIs('blogs.show'),
                $this->meta_description
            ),

            // Relationships
            'author' => new AuthorResource($this->whenLoaded('author')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
