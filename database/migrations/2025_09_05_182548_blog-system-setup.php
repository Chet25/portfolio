<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();

            // Core content
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content'); // Editor.js JSON blocks
            $table->text('excerpt')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('thumbnail')->nullable();

            // Publishing workflow
            $table->enum('status', ['draft', 'published', 'scheduled', 'archived'])->default('draft');
            $table->enum('review_status', ['pending_review', 'approved', 'rejected'])->default('pending_review');
            $table->timestamp('published_at')->nullable();

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('canonical_url')->nullable();

            // Engagement / analytics
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('likes')->default(0);
            $table->unsignedInteger('reading_time')->nullable(); // estimated minutes
            $table->boolean('is_featured')->default(false);

            // Relationships
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Author
            $table->foreignId('editor_id')->nullable()->constrained('users')->onDelete('set null'); // Reviewer/editor

            $table->timestamps();
            $table->softDeletes();

            // Add index for status + published_at for efficient published post queries
            $table->index(['status', 'published_at']);
        });

        // Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color', 20)->nullable(); // hex or Tailwind color key
            $table->timestamps();
        });

        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained('blogs')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->unique(['blog_id', 'category_id']);
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('color')->nullable();       // for tag badges
            $table->text('description')->nullable();   // optional tag description
            $table->timestamps();
        });

        // Pivot table for blogs ↔ tags
        Schema::create('blog_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained('blogs')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
            $table->unique(['blog_id', 'tag_id']);
            $table->timestamps();
        });

        // Comments
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained('blogs')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade'); // For threaded/nested comments
            $table->text('content');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->string('author_name')->nullable();
            $table->string('author_email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
        Schema::dropIfExists('blog_tags');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('blog_categories');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('blogs');
    }
};
