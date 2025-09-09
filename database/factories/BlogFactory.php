<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogFactory extends Factory
{
    protected $model = Blog::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(6, true);

        return [
            'title'         => $title,
            'slug'          => Str::slug($title) . '-' . Str::random(5),
            'content'       => json_encode([
                'time'   => now()->timestamp,
                'blocks' => [
                    [
                        'type' => 'paragraph',
                        'data' => ['text' => $this->faker->paragraphs(3, true)],
                    ]
                ],
            ]),
            'excerpt'       => $this->faker->sentence(20),
            'featured_image' => $this->faker->imageUrl(1200, 800, 'blog', true),
            'thumbnail'     => $this->faker->imageUrl(400, 300, 'thumb', true),

            'status'        => $this->faker->randomElement(['draft', 'published', 'scheduled']),
            'review_status' => $this->faker->randomElement(['pending_review', 'approved', 'rejected']),
            'published_at'  => $this->faker->boolean(70) ? $this->faker->dateTimeBetween('-1 year', 'now') : null,

            'meta_title'       => $this->faker->sentence(6),
            'meta_description' => $this->faker->text(160),
            'canonical_url'    => $this->faker->url(),

            'views'         => $this->faker->numberBetween(0, 5000),
            'likes'         => $this->faker->numberBetween(0, 1000),
            'reading_time'  => $this->faker->numberBetween(2, 15),
            'is_featured'   => $this->faker->boolean(15),

            'user_id'   => User::factory(),   // Author
            'editor_id' => User::factory(),   // Reviewer
        ];
    }
}
