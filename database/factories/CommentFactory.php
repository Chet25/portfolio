<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'blog_id'  => Blog::factory(),
            'user_id'  => $this->faker->boolean(70) ? User::factory() : null,
            'parent_id' => null, // can be set manually for nesting

            'content'  => $this->faker->paragraph(),
            'status'   => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'author_name'  => $this->faker->name(),
            'author_email' => $this->faker->safeEmail(),
        ];
    }
}
