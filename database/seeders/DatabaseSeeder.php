<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 🔑 Create an admin user (you can assign roles later via Spatie\Permission)
        $admin = User::factory()->create([
            'name'     => 'Jakey',
            'email'    => 'jakereaper74@gmail.com',
            'password' => Hash::make('12345678'),
        ]);

        // 👥 Create some extra users
        $users = User::factory(5)->create();

        // 🏷 Create categories & tags first
        $categories = Category::factory(6)->create();
        $tags = Tag::factory(10)->create();

        // 📝 Create blogs with relationships
        Blog::factory(20)
            ->for($admin, 'author')          // use Jakey as the main author
            ->for($users->random(), 'editor') // random editor from extra users
            ->create()
            ->each(function ($blog) use ($categories, $tags, $users) {
                // attach 2 random categories
                $blog->categories()->attach($categories->random(2));

                // attach 3 random tags
                $blog->tags()->attach($tags->random(3));

                // add comments (some guest, some from users)
                \App\Models\Comment::factory(5)->create([
                    'blog_id' => $blog->id,
                    'user_id' => $users->random()->id ?? null,
                ]);
            });
    }
}
