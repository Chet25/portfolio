<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogCollection;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class BlogController extends Controller
{
    /**
     * List all published blogs (public).
     */
    public function index(Request $request): BlogCollection
    {
        $validated = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
            'category' => ['sometimes', 'string', 'exists:categories,slug'],
            'tag' => ['sometimes', 'string', 'exists:tags,slug'],
            'featured' => ['sometimes', 'boolean'],
            'search' => ['sometimes', 'string', 'max:100'],
            'sort' => ['sometimes', 'string', Rule::in(['latest', 'oldest', 'popular', 'trending'])],
        ]);

        $cacheKey = 'blogs:public:' . md5(serialize($validated) . $request->get('page', 1));

        $blogs = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($validated) {
            $query = Blog::query()
                ->published()
                ->with(['author', 'categories', 'tags']);

            if (isset($validated['category'])) {
                $query->whereHas('categories', fn($q) => $q->where('slug', $validated['category']));
            }

            if (isset($validated['tag'])) {
                $query->whereHas('tags', fn($q) => $q->where('slug', $validated['tag']));
            }

            if (isset($validated['featured']) && $validated['featured']) {
                $query->where('is_featured', true);
            }

            if (isset($validated['search'])) {
                $search = $validated['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('excerpt', 'like', "%{$search}%");
                });
            }

            $query->orderBy(match ($validated['sort'] ?? 'latest') {
                'oldest' => 'published_at',
                'popular' => 'views',
                'trending' => 'likes',
                default => 'published_at',
            }, match ($validated['sort'] ?? 'latest') {
                'oldest' => 'asc',
                default => 'desc',
            });

            return $query->paginate($validated['per_page'] ?? 15);
        });

        return new BlogCollection($blogs);
    }

    /**
     * Show a single published blog (public).
     */
    public function show(string $slug): BlogResource|JsonResponse
    {
        $cacheKey = "blogs:public:show:{$slug}";

        $blog = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($slug) {
            return Blog::query()
                ->published()
                ->with(['author', 'categories', 'tags'])
                ->where('slug', $slug)
                ->first();
        });

        if (! $blog) {
            return response()->json([
                'message' => 'Blog not found.',
            ], 404);
        }

        // Increment views (non-blocking, outside cache)
        Blog::where('id', $blog->id)->increment('views');

        return new BlogResource($blog);
    }

    /**
     * Get featured blogs (public).
     */
    public function featured(Request $request): BlogCollection
    {
        $limit = $request->validate([
            'limit' => ['sometimes', 'integer', 'min:1', 'max:10'],
        ])['limit'] ?? 5;

        $blogs = Cache::remember('blogs:featured', now()->addMinutes(10), function () {
            return Blog::query()
                ->featured()
                ->with(['author', 'categories', 'tags'])
                ->orderByDesc('published_at')
                ->limit(10)
                ->get();
        });

        return new BlogCollection($blogs->take($limit));
    }
}
